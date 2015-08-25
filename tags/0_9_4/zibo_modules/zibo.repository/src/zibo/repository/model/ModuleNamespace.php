<?php

namespace zibo\repository\model;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Namespace data container
 */
class ModuleNamespace {

    /**
     * Icon for this data object
     * @var string
     */
    const ICON = 'web/images/repository/namespace.png';

    /**
     * Name of the namespace
     * @var string
     */
    private $name;

    /**
     * Modules of this namespace
     * @var array
     */
    private $modules;

    /**
     * Constructs a new namespace container
     * @param string $name Name of the namespace
     * @return null
     */
    public function __construct($name) {
        $this->setName($name);
    }

    /**
     * Sets the name of the namespace
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     */
    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        $this->name = $name;
    }

    /**
     * Gets the name of the namespace
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Adds a module to this namespace
     * @param Module $module Module to add
     * @return null
     */
    public function addModule(Module $module) {
        $this->modules[$module->getName()] = $module;
    }

    /**
     * Removes a module from the namespace
     * @param Module $module Module to remove
     * @return null
     */
    public function removeModule(Module $module) {
        $name = $module->getName();

        if (array_key_exists($name, $this->modules)) {
            unset($this->modules[$name]);
        }
    }

    /**
     * Gets all the modules of this namespace
     * @return array
     */
    public function getModules() {
        ksort($this->modules);
        return $this->modules;
    }

    /**
     * Gets a module from this namespace
     * @param string $name The name of the module
     * @return Module
     */
    public function getModule($name) {
        if (!array_key_exists($name, $this->modules)) {
            throw new ZiboException('Could not get module ' . $this->name . '.' . $name . ': the module does not exist in this namespace');
        }

        return $this->modules[$name];
    }

    /**
     * Gets whether this namespace has modules available
     * @return boolean True if the namespace has modules, false otherwise
     */
    public function hasModules() {
        return !empty($this->modules);
    }

    /**
     * Checks if this namespace has the provided module
     * @param string $name The name of the module
     * @return boolean
     */
    public function hasModule($name) {
        if (!$this->modules) {
            return false;
        }

        return array_key_exists($name, $this->modules);
    }

    /**
     * Gets the number of modules in this namespace
     * @return integer
     */
    public function countModules() {
        return count($this->modules);
    }

}