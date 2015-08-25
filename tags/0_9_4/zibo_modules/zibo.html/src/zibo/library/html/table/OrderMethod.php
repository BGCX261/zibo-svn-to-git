<?php

namespace zibo\library\html\table;

use zibo\library\Callback;

/**
 * Definition of a order method of an extended table
 */
class OrderMethod {

    /**
     * Callback to order ascending
     * @var zibo\library\Callback
     */
    private $callbackAscending;

    /**
     * Callback to order descending
     * @var zibo\library\Callback
     */
    private $callbackDescending;

    /**
     * Array with arguments for the callbacks
     * @var array
     */
    private $arguments;

    /**
     * Constructs a new order method
     * @param string|array|zibo\library\Callback $callbackAscending Callback to order ascending
     * @param string|array|zibo\library\Callback $callbackDescending Callback to order descending
     * @param array $arguments Arguments for the callback
     * @return null
     */
    public function __construct($callbackAscending, $callbackDescending, array $arguments = null) {
        $this->callbackAscending = new Callback($callbackAscending);
        $this->callbackDescending = new Callback($callbackDescending);
        $this->arguments = $arguments;
    }

    /**
     * Invokes the ascending callback with the provided values
     * @param array $values Values to order ascending
     * @return array Array with the values ordered ascending
     */
    public function invokeAscending(array $values) {
        return $this->invoke($this->callbackAscending, $values);
    }

    /**
     * Invokes the descending callback with the provided values
     * @param array $values Values to order descending
     * @return array Array with the values ordered descending
     */
    public function invokeDescending(array $values) {
        return $this->invoke($this->callbackDescending, $values);
    }

    /**
     * Invokes the callback with the provided values
     * @param zibo\library\Callback $calback Callback to order the values
     * @param array $values Values to order
     * @return array Array with the values ordered
     */
    private function invoke(Callback $callback, $values) {
        if (!$this->arguments) {
            return $callback->invoke($values);
        }

        $arguments = $this->arguments;
        array_unshift($arguments, $values);

        return $callback->invokeWithArrayArguments($arguments);
    }

}