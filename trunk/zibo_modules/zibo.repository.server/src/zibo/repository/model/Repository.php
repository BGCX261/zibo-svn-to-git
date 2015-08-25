<?php

namespace zibo\repository\model;

use zibo\admin\model\module\exception\ModuleFileNotFoundException;
use zibo\admin\model\module\io\XmlModuleIO;
use zibo\admin\model\module\Installer;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\xml\dom\Document;
use zibo\library\String;

use zibo\repository\model\exception\ModuleNotFoundException;
use zibo\repository\model\exception\ModuleVersionAlreadyExistsException;
use zibo\repository\Module as RepositoryModule;

use zibo\ZiboException;

use \DOMXPath;
use \Exception;

/**
 * Logic of the Zibo module repository
 */
class Repository {

    /**
     * Configuration key for the path of the RNG schema of the repository
     * @var string
     */
    const CONFIG_REPOSITORY_RNG = 'schema.repository';

    /**
     * Name of the index file in the repository directory
     * @var string
     */
    const INDEX_FILE = 'index.xml';

    /**
     * Instance of the repository directory
     * @var zibo\library\filesystem\File
     */
    private $directory;

    /**
     * The DOM document of the index
     * @var zibo\library\xml\dom\Document
     */
    private $indexDom;

    /**
     * Flag to see if the index needs to be updated
     * @var boolean
     */
    private $isIndexDirty;

    /**
     * Status code when a version of a module has been removed
     * @var integer
     */
    const DELETED_VERSION = 1;

    /**
     * Status code when a complete module has been removed
     * @var integer
     */
    const DELETED_MODULE = 2;

    /**
     * Constructs a new repository
     * @param zibo\library\filesystem\File $directory The directory of the repository
     * @return null
     * @throws zibo\ZiboException when the provided directory does not exist or when it's not writable
     */
    public function __construct(File $directory) {
        $directory->create();
        if (!$directory->isDirectory()) {
            throw new ZiboException($directory->getAbsolutePath() . ' is not a directory');
        }
        if (!$directory->isWritable()) {
            throw new ZiboException($directory->getAbsolutePath() . ' is not writable');
        }

        $this->directory = $directory;
        $this->isIndexDirty = false;

        $this->readIndexFile();
    }

    /**
     * Destructs the repository, makes sure the index is saved
     * @return null
     */
    public function __destruct() {
        if (!$this->isIndexDirty) {
            return;
        }

        $indexFile = new File($this->directory, self::INDEX_FILE);
        $this->indexDom->save($indexFile);
    }

    /**
     * Adds a module to the repository
     * @param zibo\library\filesystem\File $pharFile Path to the phar file of the module
     * @return array Array with Module instances of the installed modules
     */
    public function addModule(File $pharFile) {
        $moduleIO = new XmlModuleIO();
        $modules = $moduleIO->readModules($pharFile);

        $zibo = Zibo::getInstance();

        foreach ($modules as $module) {
            $namespace = $module->getNamespace();
            $name = $module->getName();
            $version = $module->getVersion();
            $ziboVersion = $module->getZiboVersion();

            $zibo->runEvent(Zibo::EVENT_LOG, $namespace. '.' . $name . ' ' . $version, '', 0, RepositoryModule::LOG_NAME);

            $query = $this->createQuery();
            $xpath = new DOMXPath($this->indexDom);
            $modulesElement = $xpath->query($query)->item(0);

            $query = $this->createModuleQuery($namespace, $name);
            $moduleElement = $xpath->query($query, $modulesElement)->item(0);

            $zibo->runEvent(Zibo::EVENT_LOG, 'module element found?', is_object($moduleElement) ? $moduleElement->ownerDocument->saveXML($moduleElement) : $moduleElement);

            if (!$moduleElement) {
                $moduleElement = $this->indexDom->createElement(RepositoryModule::TAG_MODULE);
                $moduleElement->setAttribute(RepositoryModule::ATTRIBUTE_NAMESPACE, $namespace);
                $moduleElement->setAttribute(RepositoryModule::ATTRIBUTE_NAME, $name);
                $modulesElement->appendChild($moduleElement);
            } else {
                $query = $this->createVersionQuery($version);
                $versionElement = $xpath->query($query, $moduleElement)->item(0);
                if ($versionElement) {
                    $zibo->runEvent(Zibo::EVENT_LOG, 'version element found?', is_object($versionElement) ? $versionElement->ownerDocument->saveXML($versionElement) : $versionElement);
                    throw new ModuleVersionAlreadyExistsException("Version $version of module $namespace/$name already exists in the repository", $namespace, $name, $version);
                }
            }

            $versionElement = $this->indexDom->createElement(RepositoryModule::TAG_VERSION);
            $versionElement->setAttribute(RepositoryModule::ATTRIBUTE_VERSION, $version);
            $versionElement->setAttribute(RepositoryModule::ATTRIBUTE_ZIBO_VERSION, $ziboVersion);

            $dependencies = $module->getDependencies();
            foreach ($dependencies as $dependency) {
                $dependencyElement = $this->indexDom->createElement(RepositoryModule::TAG_DEPENDENCY);
                $dependencyElement->setAttribute(RepositoryModule::ATTRIBUTE_NAMESPACE, $dependency->getNamespace());
                $dependencyElement->setAttribute(RepositoryModule::ATTRIBUTE_NAME, $dependency->getName());
                $dependencyElement->setAttribute(RepositoryModule::ATTRIBUTE_VERSION, $dependency->getVersion());
                $versionElement->appendChild($dependencyElement);

                // should we also check if the dependencies are met in the repository, otherwise fail and show why ???
                // dependency version acts as a minimal version AFAIK
            }

            // search the first existing version that has a version number lower than the one we want to insert
            // we will insert the new version before that existing version, to keep the version with the highest version number at the top
            $firstLowerVersionElement = null;
            foreach ($moduleElement->childNodes as $existingVersionElement) {
                if (version_compare($existingVersionElement->getAttribute(RepositoryModule::ATTRIBUTE_VERSION), $version) === -1) {
                    $firstLowerVersionElement = $existingVersionElement;
                    break;
                }
            }

            if ($firstLowerVersionElement) {
                $moduleElement->insertBefore($versionElement, $firstLowerVersionElement);
            } else {
                $moduleElement->appendChild($versionElement);
            }

            $moduleFile = $this->getModuleFile($namespace, $name, $version);
            $pharFile->copy($moduleFile);
        }

        $this->isIndexDirty = true;

        return $modules;
    }

    /**
     * Gets all the namespaces in the repository
     * @return array Array with ModuleNamespace objects
     */
    public function getNamespaces() {
        $query = $this->createQuery();

        $xpath = new DOMXPath($this->indexDom);
        $modulesElement = $xpath->query($query)->item(0);

        $namespaces = array();

        foreach ($modulesElement->childNodes as $moduleElement) {
            $namespace = $moduleElement->getAttribute(RepositoryModule::ATTRIBUTE_NAMESPACE);

            if (!isset($namespaces[$namespace])) {
                $namespaces[$namespace] = new ModuleNamespace($namespace);
            }

            $namespaces[$namespace]->addModule($this->getModuleFromElement($moduleElement));
        }

        ksort($namespaces);

        return $namespaces;
    }

    /**
     * Gets all the module for the provided namespace
     * @param string $namespace The namespace to get the modules of
     * @return array Array with Module objects
     */
    public function getModules($namespace) {
        $this->checkArguments($namespace);

        $modules = array();

        $query = $this->createQuery($namespace);

        $xpath = new DOMXPath($this->indexDom);
        $result = $xpath->query($query);

        for ($i = 0; $i < $result->length; $i++) {
            $moduleElement = $result->item($i);

            $module = $this->getModuleFromElement($moduleElement);
            $modules[$module->getName()] = $module;
        }

        ksort($modules);

        return $modules;
    }

    /**
     * Gets a module from the repository
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return Module instance of the module
     * @throws zibo\repository\model\exception\ModuleNotFoundException
     */
    public function getModule($namespace, $name) {
        $this->checkArguments($namespace, $name);

        $moduleElement = $this->getModuleElement($namespace, $name);

        return $this->getModuleFromElement($moduleElement);
    }

    /**
     * Retrieves a module instance from the module DOMElement
     * @param DOMElement $moduleElement The DOM element of the module
     * @return Module Instance of the module
     */
    private function getModuleFromElement($moduleElement) {
        $namespace = $moduleElement->getAttribute(RepositoryModule::ATTRIBUTE_NAMESPACE);
        $name = $moduleElement->getAttribute(RepositoryModule::ATTRIBUTE_NAME);

        $module = null;
        foreach ($moduleElement->childNodes as $versionElement) {
            $dependencies = array();
            foreach ($versionElement->childNodes as $dependencyElement) {
                $dependencyNamespace = $dependencyElement->getAttribute(RepositoryModule::ATTRIBUTE_NAMESPACE);
                $dependencyName = $dependencyElement->getAttribute(RepositoryModule::ATTRIBUTE_NAME);
                $dependencyVersion = $dependencyElement->getAttribute(RepositoryModule::ATTRIBUTE_VERSION);
                $dependency = new Module($dependencyNamespace, $dependencyName, $dependencyVersion);
                $dependencies[] = $dependency;
            }

            $version = $versionElement->getAttribute(RepositoryModule::ATTRIBUTE_VERSION);
            $ziboVersion = $versionElement->getAttribute(RepositoryModule::ATTRIBUTE_ZIBO_VERSION);

            $versionModule = new Module($namespace, $name, $version, $ziboVersion, $dependencies);
            if ($module === null) {
                $version = new Module($namespace, $name, $version, $ziboVersion, $dependencies);
                $module = $versionModule;
                $module->addVersion($version);
            } else {
                $module->addVersion($versionModule);
            }
        }

        return $module;
    }

    /**
     * Gets the file of the provided module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return zibo\library\filesystem\File Path to the module in the repository
     * @throws zibo\repository\model\exception\ModuleNotFoundException
     */
    public function getModuleFileForLatestVersion($namespace, $name) {
        $this->checkArguments($namespace, $name);

        $module = $this->getModule($namespace, $name);

        return $this->getModuleFile($namespace, $name, $module->getVersion());
    }

    /**
     * Gets the file of the provided module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @return zibo\library\filesystem\File Path to the module in the repository
     * @throws zibo\repository\model\exception\ModuleNotFoundException
     */
    public function getModuleFileForVersion($namespace, $name, $version) {
        $this->checkArguments($namespace, $name, $version);

        $this->getModuleVersionElement($namespace, $name, $version);

        return $this->getModuleFile($namespace, $name, $version);
    }

    /**
     * Gets the file of the provided module with at least the provided version
     * @param string $namespace
     * @param string $name
     * @param string $atLeastVersion
     * @return zibo\library\filesystem\File Path to the module in the repository
     * @throws zibo\repository\model\exception\ModuleNotFoundException
     */
    public function getModuleFileForVersionAtLeast($namespace, $name, $atLeastVersion) {
        $this->checkArguments($namespace, $name, $atLeastVersion);

        $moduleElement = $this->getModuleElement($namespace, $name);

        foreach ($moduleElement->childNodes as $versionElement) {
            $version = $versionElement->getAttribute(RepositoryModule::ATTRIBUTE_VERSION);
            if (version_compare($version, $atLeastVersion) !== -1 ) {
                return $this->getModuleFile($namespace, $name, $version);
            }
        }

        throw new ModuleNotFoundException($namespace, $name, $atLeastVersion, true);
    }

    /**
     * Removes a version of the provided module from the repository
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version to remove
     * @return integer The status of the removal
     * @throws zibo\ZiboException when an invalid argument has been provided
     * @see DELETED_MODULE
     * @see DELETED_VERSION
     */
    public function deleteModuleVersion($namespace, $name, $version) {
        $this->checkArguments($namespace, $name, $version);

        $versionElement = $this->getModuleVersionElement($namespace, $name, $version);

        $moduleElement = $versionElement->parentNode;
        $moduleElement->removeChild($versionElement);

        // also remove the module if no versions are left
        if ($moduleElement->childNodes->length === 0) {
            $moduleElement->parentNode->removeChild($moduleElement);
            $status = self::DELETED_MODULE;
        } else {
            $status = self::DELETED_VERSION;
        }

        $this->isIndexDirty = true;

        $repositoryFile = $this->getModuleFile($namespace, $name, $version);
        $repositoryFile->delete();

        return $status;
    }

    /**
     * Gets the new path in the repository for the provided module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @return zibo\library\filesystem\File
     */
    private function getModuleFile($namespace, $name, $version) {
        return new File($this->directory, $namespace . '.' . $name . '-' . $version . '.phar');
    }

    /**
     * Gets the DOM element for the provided module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @return DOMElement
     * @throws zibo\repository\model\exception\ModuleNotFoundException
     */
    private function getModuleElement($namespace, $name) {
        $query = $this->createQuery($namespace, $name);

        $xpath = new DOMXPath($this->indexDom);
        $element = $xpath->query($query)->item(0);

        if (!$element) {
            throw new ModuleNotFoundException($namespace, $name);
        }

        return $element;
    }

    /**
     * Gets the DOM element for the provided version of a module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @return DOMElement
     * @throws zibo\repository\model\exception\ModuleNotFoundException
     */
    private function getModuleVersionElement($namespace, $name, $version) {
        $query = $this->createQuery($namespace, $name, $version);

        $xpath = new DOMXPath($this->indexDom);
        $element = $xpath->query($query)->item(0);

        if (!$element) {
            throw new ModuleNotFoundException($namespace, $name, $version);
        }

        return $element;
    }

    /**
     * Creates a XPath query for the provided attributes, no arguments
     * @param string $namespace The namespace for the query
     * @param string $name The module name for the query
     * @param string $version The version for the query
     * @return string A XPath query for the provided arguments
     */
    private function createQuery($namespace = null, $name = null, $version = null) {
        $query = '/' . RepositoryModule::TAG_REPOSITORY . '/' . RepositoryModule::TAG_MODULES;
        if ($namespace === null) {
            return $query;
        }

        $query .= '/' . $this->createModuleQuery($namespace, $name);
        if ($version === null) {
            return $query;
        }

        $query .= '/' . $this->createVersionQuery($version);

        return $query;
    }

    /**
     * Creates a XPath query string for a module or namespace
     * @param string $namespace The namespace to query
     * @param string $name The name to query
     * @return string The XPath query string for the provided namespace or module
     */
    private function createModuleQuery($namespace, $name = null) {
        $query = RepositoryModule::TAG_MODULE;
        $query .= '[@' . RepositoryModule::ATTRIBUTE_NAMESPACE . "='" . $namespace;
        if ($name) {
            $query .= "' and @" . RepositoryModule::ATTRIBUTE_NAME . "='" . $name;
        }
        $query .= "']";

        return $query;
    }

    /**
     * Creates a XPath query string for the version attribute
     * @param string $version The version attribute
     * @return string The XPath query string for the version attribute
     */
    private function createVersionQuery($version) {
        return RepositoryModule::TAG_VERSION . '[@' . RepositoryModule::ATTRIBUTE_VERSION . "='" . $version . "']";
    }

    /**
     * Reads the index of this repository
     * @return null
     */
    private function readIndexFile() {
        $indexFile = new File($this->directory, self::INDEX_FILE);
        $dom = new Document('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        if (!$indexFile->exists()) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Initializing new index file', $indexFile, 0, RepositoryModule::LOG_NAME);

            $modulesElement = $dom->createElement(RepositoryModule::TAG_MODULES);
            $repositoryElement = $dom->createElement(RepositoryModule::TAG_REPOSITORY);
            $repositoryElement->appendChild($modulesElement);
            $dom->appendChild($repositoryElement);
        } else {
            $dom->setRelaxNGFileFromConfig(self::CONFIG_REPOSITORY_RNG);

            $success = @$dom->load($indexFile);
            if (!$success) {
                $error = error_get_last();
                throw new ZiboException("Failed loading $indexFile into a DOM tree: " . $error['message']);
            }
        }

        $this->indexDom = $dom;
    }

    /**
     * Checks if the provided arguments are strings and not empty
     * @param string $namespace
     * @param string $name
     * @param string $version
     * @return null
     * @throws zibo\ZiboException when one of the provided arguments is empty
     */
    private function checkArguments($namespace, $name = null, $version = null) {
        $numArguments = func_num_args();

        if (String::isEmpty($namespace)) {
            throw new ZiboException('Provided namespace is empty');
        }

        if ($numArguments == 1) {
            return;
        }

        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        if ($numArguments == 2) {
            return;
        }

        if (String::isEmpty($version)) {
            throw new ZiboException('Provided version is empty');
        }
    }

}