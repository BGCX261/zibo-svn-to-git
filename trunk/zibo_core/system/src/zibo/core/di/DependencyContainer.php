<?php

namespace zibo\core\di;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Container of injection dependencies
 */
class DependencyContainer {

    /**
     * Array with the injection dependencies. The class name will be stored as
     * key and an array of possible dependencies as value
     * @var array
     */
    protected $dependencies;

    /**
     * Constructs a new injection dependency container
     * @return null
     */
    public function __construct() {
        $this->dependencies = array();
    }

    /**
     * Adds a dependency for the provided class to this container
     * @param string $for A full class name
     * @param Dependency $dependency
     * @return null
     */
    public function addDependency($for, Dependency $dependency) {
        if (!String::isString($for, String::NOT_EMPTY)) {
            throw new ZiboException('Invalid for class name provided');
        }

        $id = $dependency->getId();

        if (!isset($this->dependencies[$for])) {
            $this->dependencies[$for] = array();
        }

        if ($id) {
            $this->dependencies[$for][$id] = $dependency;
        } else {
            $this->dependencies[$for][] = $dependency;

            $ids = array_keys($this->dependencies[$for]);
            $dependency->setId(array_pop($ids));
        }
    }

    /**
     * Gets the dependencies for the provided class
     * @param string $for a full class name
     * @return array Array with the class name as key and an array of
     * injection dependencies as value if no class name provided. If a
     * $for is provided, an plain array with injection dependencies
     * will be returned.
     * @see Dependency
     */
    public function getDependencies($for = null) {
        if (!$for) {
            return $this->dependencies;
        }

        if (isset($this->dependencies[$for])) {
            return $this->dependencies[$for];
        }

        return array();
    }

}