<?php

namespace zibo\library;

use zibo\ZiboException;

/**
 * Callback object
 */
class Callback {

    /**
     * The callback to wrap around
     * @var string|array
     */
    private $callback;

    /**
     * A string representation of the callback
     * @var string
     */
    private $callbackString;

    /**
     * Constructs a new callback
     * @param string|array|Callback $callback The callback
     * @return null
     * @throws zibo\ZiboException when the provided callback is invalid
     */
    public function __construct($callback) {
        $this->setCallback($callback);

        if (!is_callable($this->callback)) {
            throw new ZiboException('Provided callback is not callable: ' . $this->callbackString);
        }
    }

    /**
     * Gets a string representation of this callback
     * @return string
     */
    public function __toString() {
        return $this->callbackString;
    }

    /**
     * Sets the callback
     * @param string|array|Callback $callback The callback
     * @return null
     * @throws zibo\ZiboException when an invalid callback has been provided
     */
    private function setCallback($callback) {
        if ($callback instanceof self) {
            // callback is already an instance of Callback, copy it's variables
            $this->callback = $callback->callback;
            $this->callbackString = $callback->callbackString;

            return;
        }

        try {
            if (String::isEmpty($callback)) {
                throw new ZiboException('Provided callback is empty');
            }

            // callback is a string, a global function call
            $this->callback = $callback;
            $this->callbackString = $callback;

            return;
        } catch (ZiboException $e) {

        }

        if (!is_array($callback)) {
            throw new ZiboException('Provided callback is invalid: callback is not a string or an array');
        }
        if (count($callback) != 2) {
            throw new ZiboException('Provided callback is invalid: callback array should have 2 elements');
        }
        if (!isset($callback[0])) {
            throw new ZiboException('Provided callback is invalid: callback array should have an element 0 containing the class name or a class instance');
        }
        if (!isset($callback[1])) {
            throw new ZiboException('Provided callback is invalid: callback array should have an element 1 containing the function name');
        }

        // callback is an array with a class name or class instance as first element and the method as the 2nd element
        $object = $callback[0];
        $function = $callback[1];

        $isInstance = false;
        if (is_object($object)) {
            $object = get_class($object);
            $isInstance = true;
        }

        try {
            if (String::isEmpty($object)) {
                throw new ZiboException('Provided callback is invalid: class parameter is empty');
            }
        } catch (ZiboException $e) {
            throw new ZiboException('Provided callback is invalid: class parameter is invalid');
        }

        try {
            if (String::isEmpty($function)) {
                throw new ZiboException('Provided callback is invalid: function parameter is empty');
            }
        } catch (ZiboException $e) {
            throw new ZiboException('Provided callback is invalid: function parameter is invalid');
        }

        $this->callback = $callback;
        $this->callbackString = $object . ($isInstance ? '->' : '::') . $function;
    }

    /**
     * Invokes the callback. All arguments passed to this method will be passed on to the callback
     * @return mixed The result of the callback
     */
    public function invoke() {
        $arguments = func_get_args();

        return $this->invokeWithArrayArguments($arguments);
    }

    /**
     * Invokes the callback with an array of arguments
     * @param array $arguments The arguments for the callback
     * @return mixed The result of the callback
     */
    public function invokeWithArrayArguments(array $arguments) {
        if (!is_callable($this->callback)) {
            throw new ZiboException('Could not invoke ' . $this->__toString() . ': callback is not callable');
        }

        return call_user_func_array($this->callback, $arguments);
    }

}