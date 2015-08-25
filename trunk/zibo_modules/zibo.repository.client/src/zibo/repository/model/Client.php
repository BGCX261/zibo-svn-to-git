<?php

namespace zibo\repository\model;

use zibo\admin\model\module\ModuleModel;
use zibo\admin\model\module\Module as AdminModelModule;
use zibo\admin\model\module\Installer;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\xmlrpc\Client as XmlRpcClient;
use zibo\library\xmlrpc\Request;
use zibo\library\xmlrpc\Value;

use zibo\repository\Module as RepositoryModule;

use zibo\ZiboException;

/**
 * Client for the Zibo module repository
 */
class Client {

    /**
     * Array with the XML-RPC clients to talk with the repositories
     * @var array
     */
    protected $clients;

    /**
     * Installer for Zibo modules
     * @var zibo\admin\model\Installer
     */
    protected $installer;

    /**
     * Constructs a new module repository client
     * @param zibo\admin\model\Installer $installer Installer for Zibo modules
     * @param string|array $urls The URL or URLs of the repository XML-RPC server(s)
     * @return null
     */
    public function __construct(Installer $installer, $urls) {
        $this->installer = $installer;

        if (!is_array($urls)) {
            $urls = array($urls);
        }

        $this->clients = array();
        foreach ($urls as $url) {
            $this->clients[$url] = new XmlRpcClient($url);
        }
    }

    /**
     * Gets the installer used by this client
     * @return zibo\admin\model\Installer
     */
    public function getInstaller() {
        return $this->installer;
    }

    /**
     * Solves the dependencies for the provided modules
     * @param array $modules Array with Module instances of the ones are to be installed
     * @return null
     */
    public function solveDependencies(array $modules) {
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Starting dependency solver', null, 0, RepositoryModule::LOG_NAME);

        foreach ($modules as $module) {
            $dependencies = $module->getDependencies();
            foreach ($dependencies as $dependency) {
                $this->solveDependenciesForModule($dependency);
            }
        }
    }

    /**
     * Solves the dependencies of the provided module
     * @param zibo\admin\model\Module $module
     * @return null
     */
    private function solveDependenciesForModule(AdminModelModule $module) {
        $namespace = $module->getNamespace();
        $name = $module->getName();
        $version = $module->getVersion();

        if (!$this->installer->hasModule($namespace, $name)) {
            $this->installModuleVersionAtLeast($namespace, $name, $version);
            return;
        }

        $installedModule = $this->installer->getModule($namespace, $name);
        if (version_compare($version, $installedModule->getVersion()) == 1) {
            $this->installModuleVersionAtLeast($namespace, $name, $version);
        }
    }

    /**
     * Installs a module, with the latest version in the repository, on the system
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return null
     */
    public function installModule($namespace, $name) {
        $request = new Request(RepositoryModule::SERVICE_PREFIX . RepositoryModule::SERVICE_MODULE_VERSION_LATEST);
        $request->addParameter(new Value($namespace));
        $request->addParameter(new Value($name));

        $this->retrieveAndInstallModule($request, $namespace, $name);
    }

    /**
     * Installs a module, with a provided specific version, on the system
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $minVersion The minimum version to install
     * @return null
     */
    public function installModuleVersion($namespace, $name, $version) {
        $request = new Request(RepositoryModule::SERVICE_PREFIX . RepositoryModule::SERVICE_MODULE_VERSION);
        $request->addParameter(new Value($namespace));
        $request->addParameter(new Value($name));
        $request->addParameter(new Value($version));

        $this->retrieveAndInstallModule($request, $namespace, $name);
    }

    /**
     * Installs a module, with a provided minimum version, on the system
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $minVersion The minimum version to install
     * @return null
     */
    private function installModuleVersionAtLeast($namespace, $name, $minVersion) {
        Zibo::getInstance()->runEvent('log', "Trying to install module $namespace.$name $minVersion from the repository");

        $request = new Request(RepositoryModule::SERVICE_PREFIX . RepositoryModule::SERVICE_MODULE_VERSION_AT_LEAST);
        $request->addParameter(new Value($namespace));
        $request->addParameter(new Value($name));
        $request->addParameter(new Value($minVersion));

        $this->retrieveAndInstallModule($request, $namespace, $name);
    }

    /**
     * Invokes the provided module request and install the received module
     * @param zibo\library\xmlrpc\Request $request XML-RPC request to retrieve the module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return array Array with the installed modules
     */
    private function retrieveAndInstallModule(Request $request, $namespace, $name) {
        $exception = null;

        foreach ($this->clients as $url => $client) {
            $response = $client->invoke($request);

            if ($response->getErrorCode() !== 0) {
                if (!$exception) {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage());
                } else {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage(), 0, $exception);
                }

                continue;
            }

            $moduleFileContent = base64_decode($response->getValue());
            $this->installModuleLocally($moduleFileContent, $namespace, $name);

            $exception = null;
            break;
        }

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Installs the provided module on the filesystem
     * @param string $moduleFileContent Decoded Base64 of the contents of the module phar file
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return null
     * @throws Exception when an error occured
     */
    private function installModuleLocally($moduleFileContent, $namespace, $name) {
        if ($moduleFileContent === '') {
            throw new ZiboException('No sources for the module ' . $namespace . '.' . $name . ' recieved from the repository');
        }

        $downloadDirectory = new File('application/data');
        $downloadDirectory->create();

        $moduleFile = new File($downloadDirectory, $namespace . '.' . $name. '.phar');
        $moduleFile->write($moduleFileContent);

        $exception = null;

        try {
            $this->installer->installModule($moduleFile);
        } catch (Exception $e) {
            $exception = $e;
        }

        if ($moduleFile->exists()) {
            $moduleFile->delete();
        }

        if ($exception) {
            throw $exception;
        }
    }

    /**
     * Gets the namespaces available in the repository
     * @return array Array with the name of the namespace as key and an instance of ModuleNamespace as value
     */
    public function getNamespaces() {
        $namespaces = array();
        $exception = null;

        $request = new Request(RepositoryModule::SERVICE_PREFIX . RepositoryModule::SERVICE_NAMESPACES_INFO);

        foreach ($this->clients as $url => $client) {
            $response = $client->invoke($request);

            if ($response->getErrorCode() !== 0) {
                if (!$exception) {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage());
                } else {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage(), 0, $exception);
                }

                continue;
            }

            $result = $response->getValue();
            foreach ($result as $namespaceStruct) {
                $namespaceName = $namespaceStruct[RepositoryModule::ATTRIBUTE_NAME];

                if (!array_key_exists($namespaceName, $namespaces)) {
                    $namespaces[$namespaceName] = new ModuleNamespace($namespaceName);
                }

                foreach ($namespaceStruct[RepositoryModule::TAG_MODULES] as $moduleStruct) {
                    $module = $this->getModuleFromArray($moduleStruct);
                    $module->setRepository($url);
                    $moduleName = $module->getName();

                    if ($namespaces[$namespaceName]->hasModule($moduleName)) {
                        $namespaceModule = $namespaces[$namespaceName]->getModule($moduleName);
                        $namespaceModule->addVersion($module);
                    } else {
                        $namespaces[$namespaceName]->addModule($module);
                    }
                }
            }
        }

        return $namespaces;
    }

    /**
     * Gets a namespace from the repository
     * @param string $namespace The namespace to retrieve
     * @return ModuleNamespace
     * @throws Exception when the namespace could not be retrieved
     */
    public function getNamespace($namespace) {
        $request = new Request(RepositoryModule::SERVICE_PREFIX . RepositoryModule::SERVICE_NAMESPACE_INFO);
        $request->addParameter(new Value($namespace));

        $exception = null;
        $namespace = new ModuleNamespace($namespace);

        foreach ($this->clients as $url => $client) {
            $response = $client->invoke($request);

            if ($response->getErrorCode() !== 0) {
                if (!$exception) {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage());
                } else {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage(), 0, $exception);
                }

                continue;
            }

            $namespaceStruct = $response->getValue();
            foreach ($namespaceStruct[RepositoryModule::TAG_MODULES] as $moduleStruct) {
                $module = $this->getModuleFromArray($moduleStruct);
                $module->setRepository($url);
                $moduleName = $module->getName();

                if ($namespace->hasModule($moduleName)) {
                    $namespaceModule = $namespace->getModule($moduleName);
                    $namespaceModule->addVersion($module);
                } else {
                    $namespace->addModule($module);
                }
            }
        }

        if ($exception) {
            throw $exception;
        }

        return $namespace;
    }

    /**
     * Gets a module from the repository
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return Module
     * @throws Exception when the module could not be retrieved
     */
    public function getModule($namespace, $name) {
        $request = new Request(RepositoryModule::SERVICE_PREFIX . RepositoryModule::SERVICE_MODULE_INFO);
        $request->addParameter(new Value($namespace));
        $request->addParameter(new Value($name));

        $exception = null;
        $module = null;

        foreach ($this->clients as $url => $client) {
            $response = $client->invoke($request);

            if ($response->getErrorCode() !== 0) {
                if (!$exception) {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage());
                } else {
                    $exception = new ZiboException('Repository (' . $url . ') returned a fault with code ' . $response->getErrorCode() . ' and message: ' . $response->getErrorMessage(), 0, $exception);
                }

                continue;
            }

            $module = $this->getModuleFromArray($response->getValue());
            $module->setRepository($url);

            break;
        }

        if ($exception) {
            throw $exception;
        }

        return $module;
    }

    /**
     * Gets the module from the provided module array
     * @param array $moduleArray A array with the module properties, as retrieved from the webservices
     * @return Module
     */
    private function getModuleFromArray(array $moduleArray) {
        $namespace = $moduleArray[RepositoryModule::ATTRIBUTE_NAMESPACE];
        $name = $moduleArray[RepositoryModule::ATTRIBUTE_NAME];

        $version = null;
        if (array_key_exists(RepositoryModule::ATTRIBUTE_VERSION, $moduleArray)) {
            $version = $moduleArray[RepositoryModule::ATTRIBUTE_VERSION];
        }

        $ziboVersion = null;
        if (array_key_exists(RepositoryModule::ATTRIBUTE_ZIBO_VERSION, $moduleArray)) {
            $ziboVersion = $moduleArray[RepositoryModule::ATTRIBUTE_ZIBO_VERSION];
        }

        $dependencies = array();
        if (array_key_exists(RepositoryModule::TAG_DEPENDENCY, $moduleArray)) {
            $dependencies = $this->getModulesFromArray($moduleArray[RepositoryModule::TAG_DEPENDENCY]);
        }

        $module = new Module($namespace, $name, $version, $ziboVersion, $dependencies);

        if (array_key_exists(RepositoryModule::TAG_VERSIONS, $moduleArray)) {
            foreach ($moduleArray[RepositoryModule::TAG_VERSIONS] as $moduleVersionArray) {
                $moduleVersion = $this->getModuleFromArray($moduleVersionArray);
                $module->addVersion($moduleVersion);
            }
        }

        return $module;
    }

    /**
     * Gets the modules from the provided modules array
     * @param array $modulesArray Array with module arrays
     * @return Module
     */
    private function getModulesFromArray(array $modulesArray) {
        $modules = array();

        foreach ($modulesArray as $moduleArray) {
            $modules[] = $this->getModuleFromArray($moduleArray);
        }

        return $modules;
    }

}