<?php

namespace zibo\admin\model\module;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Data container of the module definition
 */
class Module {

    /**
     * Name of the module
     * @var string
     */
    private $name;

    /**
     * Namespace of the module
     * @var string
     */
    private $namespace;

    /**
     * Version of this module
     * @var string
     */
    private $version;

    /**
     * Needed Zibo version of this module
     * @var string
     */
    private $ziboVersion;

    /**
     * Array with Module instances of the modules on which this module depends
     * @var array
     */
    private $dependencies;

    /**
     * Array with Module instances of the modules who use this module
     * @var array
     */
    private $usage;

    /**
     * Path of this module, if installed
     * @var zibo\library\filesystem\File
     */
    private $path;

    /**
     * Constructs a new module data container
     * @param string $namespace
     * @param string $name
     * @param string $version
     * @param string $ziboVersion
     * @param array $dependencies
     * @return null
     * @throws zibo\ZiboException when the namespace is empty or not a string
     * @throws zibo\ZiboException when the name is empty or not a string
     * @throws zibo\ZiboException when the version is empty or not a string
     * @throws zibo\ZiboException when $dependencies contains something else then Module instances
     */
    public function __construct($namespace, $name, $version = '0.0.1', $ziboVersion = null, array $dependencies = array()) {
        $this->setNamespace($namespace);
        $this->setName($name);
        $this->setVersion($version);
        $this->setZiboVersion($ziboVersion);
        $this->setDependencies($dependencies);
        $this->usage = array();
    }

    /**
     * Sets the namespace of this module
     * @param string $namespace
     * @return null
     * @throws zibo\ZiboException when the namespace is empty or not a string
     */
    private function setNamespace($namespace) {
        if (String::isEmpty($namespace)) {
            throw new ZiboException('Namespace is empty');
        }
        $this->namespace = $namespace;
    }

    /**
     * Gets the namespace of this module
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Sets the name of this module
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the name is empty or not a string
     */
    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Name is empty');
        }
        $this->name = $name;
    }

    /**
     * Gets the name of this module
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the version of this module
     * @param string $version
     * @return null
     * @throws zibo\ZiboException when the name is empty or not a string
     */
    private function setVersion($version) {
        if (String::isEmpty($version)) {
            throw new ZiboException('Version is empty');
        }
        $this->version = $version;
    }

    /**
     * Gets the version of this module
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Sets the needed Zibo version of this module
     * @param string $version
     * @return null
     */
    public function setZiboVersion($version) {
        $this->ziboVersion = $version;
    }

    /**
     * Gets the needed Zibo version of this module
     * @return string
     */
    public function getZiboVersion() {
        return $this->ziboVersion;
    }

    /**
     * Sets the path of this module, this is the path where this module is installed
     * @param zibo\library\filesystem\File $path
     * @return null
     */
    public function setPath(File $path) {
        $this->path = $path;
    }

    /**
     * Gets the path where this module is installed
     * @return null|zibo\library\filesystem\File null if this module is not installed, a File instance otherwise
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Sets the dependencies of this module
     * @param array $modules Array with Module instances
     * @return null
     * @throws zibo\ZiboException when $dependencies contains something else then Module instances
     */
    public function setDependencies(array $modules) {
        $this->dependencies = array();
        foreach ($modules as $module) {
            if (!($module instanceof self)) {
                throw new ZiboException('Invalid dependency passed, must be an array of Module instances.');
            }
            $this->dependencies[] = $module;
        }
    }

    /**
     * Gets the dependencies of this module
     * @return array Array with Module instances
     */
    public function getDependencies() {
        return $this->dependencies;
    }

    /**
     * Checks if this module has dependencies
     * @return boolean true if there are dependencies, false otherwise
     */
    public function hasDependencies() {
        return !empty($this->dependencies);
    }

    /**
     * Adds the provided module to the usage of this module
     * @param Module $module
     * @return null
     */
    public function addUsage(Module $module) {
        $this->usage[] = $module;
    }

    /**
     * Removes the usage of the provided module
     * @param Module $module
     * @return null
     * @throws zibo\ZiboException when the provided module is not used by this module
     */
    public function removeUsage(Module $module) {
        foreach ($this->usage as $index => $usage) {
            if ($usage->getName() == $module->getName() && $usage->getNamespace() == $module->getNamespace()) {
                unset($this->usage[$index]);
                return;
            }
        }
        throw new ZiboException($module->getName() . ' from namespace ' . $module->getNamespace() . ' is not used by ' . $this->name . ' from namespace ' . $this->namespace);
    }

    /**
     * Gets the modules who use this module
     * @return array Array with Module instances
     */
    public function getUsage() {
        return $this->usage;
    }

    /**
     * Checks if this module is used by other modules
     * @return boolean true if there are modules who use this module, false otherwise
     */
    public function hasUsage() {
        return !empty($this->usage);
    }

}