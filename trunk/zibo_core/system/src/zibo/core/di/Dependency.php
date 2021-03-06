<?php

namespace zibo\core\di;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Definition of a depenency
 */
class Dependency {

    /**
     * The full class name of this dependency
     * @var string
     */
    protected $className;

    /**
     * The id of this definition
     * @var string
     */
    protected $id;

    /**
     * Arguments for the constructor
     * @var array
     */
    protected $constructorArguments;

    /**
     * Definitions of calls to invoke when creating this dependency
     * @var array
     */
    protected $calls;

    /**
     * Constructs a new dependency
     * @param string $className A full class name
     * @return null
     */
    public function __construct($className, $id = null) {
        $this->setClassName($className);
        $this->setId($id);
        $this->clearCalls();
    }

    /**
     * Sets the full class name of this dependency
     * @param string $className A full class name
     * @return null
     */
    public function setClassName($className) {
        if (!String::isString($className, String::NOT_EMPTY)) {
            throw new ZiboException('Provided class name is empty or invalid');
        }

        $this->className = $className;
    }

    /**
     * Gets the class of this dependency
     * @return string A full class name
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Sets the id of this dependency
     * @param string $id A identifier
     * @return null
     */
    public function setId($id = null) {
        if ($id !== null && !String::isString($id, String::NOT_EMPTY)) {
            throw new ZiboException('Provided id is empty or invalid');
        }

        $this->id = $id;
    }

    /**
     * Gets the id of this dependency
     * @return string A full class name
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the arguments for the constructor
     * @return null|array
     */
    public function getConstructorArguments() {
        return $this->constructorArguments;
    }

    /**
     * Adds a call to this dependency.
     * @param DependencyCall $call The call to add
     * @return
     */
    public function addCall(DependencyCall $call) {
        if ($call->getMethodName() == '__construct') {
            $this->constructorArguments = $call->getArguments();
            return;
        }

        $this->calls[] = $call;
    }

    /**
     * Gets all the calls which should be invoked after the instance is created
     * @return array Array of dependency calls
     * @see DependencyCall
     */
    public function getCalls() {
        return $this->calls;
    }

    /**
     * Clears all the calls of this dependency
     * @return null
     */
    public function clearCalls() {
        $this->constructorArguments = null;
        $this->calls = null;
    }

}