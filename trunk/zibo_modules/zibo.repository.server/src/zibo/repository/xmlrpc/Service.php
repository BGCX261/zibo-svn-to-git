<?php

namespace zibo\repository\xmlrpc;

use zibo\admin\model\module\Module;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\filesystem\File;
use zibo\library\xmlrpc\Server;
use zibo\library\xmlrpc\Value;

use zibo\repository\model\Repository;
use zibo\repository\Module as RepositoryModule;

/**
 * Provider for the XML-RPC webservices for the repository
 */
class Service {

    /**
     * Description for the webservice method to get the namespaces
     * @var string
     */
    const DESCRIPTION_NAMESPACES_INFO = 'Gets the information about all the namespaces with their modules in the repository.';

    /**
     * Description for the webservice method to get a namespace
     * @var string
     */
    const DESCRIPTION_NAMESPACE_INFO = 'Gets the information about a namespace with it\'s modules in the repository. (namespace)';

    /**
     * Description for the webservice method to get the module info
     * @var string
     */
    const DESCRIPTION_MODULE_INFO = 'Gets the full information about a module in the repository. (namespace, name)';

    /**
     * Description for the webservice method to download a module with the latest version
     * @var string
     */
    const DESCRIPTION_MODULE_LATEST_VERSION = 'Downloads the latest version of a module (as a phar file) from the repository. (namespace, name)';

    /**
     * Description for the webservice method to download a module with a specific version
     * @var string
     */
    const DESCRIPTION_MODULE_VERSION = 'Downloads a specific version of a module (as a phar file) from the repository. (namespace, name, version)';

    /**
     * Description for the webservice method to download a module with a minimum version
     * @var string
     */
    const DESCRIPTION_MODULE_VERSION_AT_LEAST = 'Downloads a minimum version of a module (as a phar file) from the repository. (namespace, name, version)';

    /**
     * The repository used by these webservices
     * @var zibo\repository\model\Repository
     */
    private $repository;

    /**
     * Constructs a new XML-RPC service provider
     * @param zibo\repository\model\Repository $repository The repository to use for the webservices
     * @return null
     */
    public function __construct(Repository $repository) {
        $this->repository = $repository;
    }

    /**
     * Registers the services at the provided XML-RPC server
     * @param zibo\library\xmlrpc\Server $server The XML-RPC server
     * @return null
     */
    public function registerServices(Server $server) {
        $this->registerService($server, RepositoryModule::SERVICE_NAMESPACES_INFO, Value::TYPE_ARRAY, null, self::DESCRIPTION_NAMESPACES_INFO);
        $this->registerService($server, RepositoryModule::SERVICE_NAMESPACE_INFO, Value::TYPE_STRUCT, array(Value::TYPE_STRING), self::DESCRIPTION_NAMESPACE_INFO);
        $this->registerService($server, RepositoryModule::SERVICE_MODULE_INFO, Value::TYPE_STRUCT, array(Value::TYPE_STRING, Value::TYPE_STRING), self::DESCRIPTION_MODULE_INFO);
        $this->registerService($server, RepositoryModule::SERVICE_MODULE_VERSION_LATEST, Value::TYPE_BASE64, array(Value::TYPE_STRING, Value::TYPE_STRING), self::DESCRIPTION_MODULE_LATEST_VERSION);
        $this->registerService($server, RepositoryModule::SERVICE_MODULE_VERSION, Value::TYPE_BASE64, array(Value::TYPE_STRING, Value::TYPE_STRING, Value::TYPE_STRING), self::DESCRIPTION_MODULE_VERSION);
        $this->registerService($server, RepositoryModule::SERVICE_MODULE_VERSION_AT_LEAST, Value::TYPE_BASE64, array(Value::TYPE_STRING, Value::TYPE_STRING, Value::TYPE_STRING), self::DESCRIPTION_MODULE_VERSION_AT_LEAST);
    }

    /**
     * Registers a the provided service on the provided XML-RPC servie
     * @param zibo\library\xmlrpc\Server $server
     * @param string $name The name of the method
     * @param string $returnType The type of the return value of the method
     * @param array $parameterTypes Array with the types of the arguments
     * @param string $description A description of this method
     * @return null
     */
    private function registerService(Server $server, $name, $returnType, array $parameterTypes = null, $description = null) {
        $callback = array($this, $name);
        $name = RepositoryModule::SERVICE_PREFIX . $name;
        $server->registerService($name, $callback, $returnType, $parameterTypes, $description);
    }

    /**
     * Gets information about all the namespaces with their modules
     * @return array
     */
    public function getNamespaces() {
        $namespaces = $this->repository->getNamespaces();

        $result = array();
        foreach ($namespaces as $namespaceName => $namespace) {
            $modules = array();

            $namespaceModules = $namespace->getModules();
            foreach ($namespaceModules as $module) {
                $modules[] = $this->getFullModuleArray($module);
            }

            $namespaceResult = array(
                RepositoryModule::ATTRIBUTE_NAME => $namespaceName,
                RepositoryModule::TAG_MODULES => $modules,
            );

            $result[] = $namespaceResult;
        }

        return $result;
    }

    /**
     * Gets information about a namespace with it's modules
     * @param string $namespace The namespace to get the information of
     * @return array
     */
    public function getNamespace($namespace) {
        $modules = $this->repository->getModules($namespace);

        $result = array(
            RepositoryModule::ATTRIBUTE_NAME => $namespace,
            RepositoryModule::TAG_MODULES => array(),
        );

        foreach ($modules as $module) {
            $result[RepositoryModule::TAG_MODULES][] = $this->getFullModuleArray($module);
        }

        return $result;
    }

    /**
     * Gets the full information about the provided module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return array Array with the attributes of the module
     */
    public function getModule($namespace, $name) {
        $module = $this->repository->getModule($namespace, $name);

        return $this->getFullModuleArray($module);
    }

    /**
     * Gets the provided module with a specific version
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return string Base64 encoded string of the phar file of the module
     */
    public function getModuleLatestVersion($namespace, $name) {
        $file = $this->repository->getModuleFileForLatestVersion($namespace, $name);

        return $this->getBase64FromFile($file);
    }

    /**
     * Gets the provided module with a specific version
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $atLeastVersion The version of the module
     * @return string Base64 encoded string of the phar file of the module
     */
    public function getModuleVersion($namespace, $name, $version) {
        $file = $this->repository->getModuleFileForVersion($namespace, $name, $version);

        return $this->getBase64FromFile($file);
    }

    /**
     * Gets the provided module with a minimum version
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $atLeastVersion The minimum version of the module
     * @return string Base64 encoded string of the phar file of the module
     */
    public function getModuleVersionAtLeast($namespace, $name, $atLeastVersion) {
        $file = $this->repository->getModuleFileForVersionAtLeast($namespace, $name, $atLeastVersion);

        return $this->getBase64FromFile($file);
    }

    /**
     * Gets the base64 encoded string of the contents of the provided file
     * @param zibo\library\filesystem\File $file The file to get the base64 string of
     * @return string
     * @throws zibo\library\filesystem\exception\FileSystemException when the file could not be read
     */
    private function getBase64FromFile(File $file) {
        $path = $file->getAbsolutePath();

        $contents = file_get_contents($path);
        if ($contents === false) {
            $error = error_get_last();
            throw new FileSystemException('Could not read ' . $path . ': ' . $error['message']);
        }

        return base64_encode($contents);
    }

    /**
     * Gets an array with the full module information
     * @param zibo\admin\module\Module $module The module information
     * @return array Array with the attributes of the module
     */
    private function getFullModuleArray(Module $module) {
        $dependencies = $this->getDependencyArray($module->getDependencies());
        $versions = $this->getVersionArray($module->getVersions());

        $array = $this->getModuleArray($module);
        if ($dependencies) {
            $array[RepositoryModule::TAG_DEPENDENCY] = $dependencies;
        }
        if ($versions) {
            $array[RepositoryModule::TAG_VERSIONS] = $versions;
        }

        return $array;
    }

    /**
     * Gets an array with the complete module information of the provided versions
     * @param array $versions Array with Module instances
     * @return array Array containing full module arrays
     */
    private function getVersionArray(array $versions) {
        $array = array();

        foreach ($versions as $version) {
            $dependencies = $this->getDependencyArray($version->getDependencies());

            $versionInfo = $this->getModuleArray($version);
            if ($dependencies) {
                $versionInfo[RepositoryModule::TAG_DEPENDENCY] = $dependencies;
            }

            $array[] = $versionInfo;
        }

        return $array;
    }

    /**
     * Gets an array with the basis module information of the provided dependencies
     * @param array $dependencies Array with Module instances
     * @return array Array containing basic module arrays
     */
    private function getDependencyArray(array $dependencies) {
        $array = array();

        foreach ($dependencies as $dependency) {
            $array[] = $this->getModuleArray($dependency, false);
        }

        return $array;
    }

    /**
     * Gets an array with the basis module information
     * @param zibo\admin\module\Module $module The module information
     * @return array Array with the attributes of the module
     */
    private function getModuleArray(Module $module, $addZiboVersion = true) {
        $array = array();

        $array[RepositoryModule::ATTRIBUTE_NAMESPACE] = $module->getNamespace();
        $array[RepositoryModule::ATTRIBUTE_NAME] = $module->getName();
        $array[RepositoryModule::ATTRIBUTE_VERSION] = $module->getVersion();

        if ($addZiboVersion) {
            $array[RepositoryModule::ATTRIBUTE_ZIBO_VERSION] = $module->getZiboVersion();
        }

        return $array;
    }

}