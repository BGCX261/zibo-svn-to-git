<?php

namespace zibo\core\di;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Definition of a depenency callback
 */
class DependencyCall {

    /**
     * The method name for this callback
     * @var string
     */
    protected $methodName;

    /**
     * The arguments for this callback
     * @var array
     */
    protected $arguments;

    /**
     * Constructs a new dependency callback
     * @param string $methodName The method name for the callback
     * @return null
     */
    public function __construct($methodName) {
        $this->setMethodName($methodName);
        $this->clearArguments();
    }

    /**
     * Sets the method name of this callback
     * @param string $methodName A method name
     * @return null
     */
    public function setMethodName($methodName) {
        if (!String::isString($methodName, String::NOT_EMPTY)) {
            throw new ZiboException('Provided class name is empty or invalid');
        }

        $this->methodName = $methodName;
    }

    /**
     * Gets the method name of this callback
     * @return string A method name
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * Adds a argument for this callback
     * @param DependencyCallArgument $argument
     * @return null
     */
    public function addArgument(DependencyCallArgument $argument) {
        if ($this->arguments === null) {
            $this->arguments = array();
        }

        $this->arguments[] = $argument;
    }

    /**
     * Gets the arguments of this callback
     * @return array Array with dependency call arguments
     * @see DependencyCallArgument
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Clears the arguments
     * @return null
     */
    public function clearArguments() {
        $this->arguments = null;
    }

}