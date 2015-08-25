<?php

/**
 * @package zibo-log-listener
 */
namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\library\config\Config;
use zibo\library\String;

use zibo\log\Module;

use zibo\ZiboException;

use \Exception;
use \ReflectionClass;
use \ReflectionException;

class LogListenerFactory {

    private $listeners;
    private $zibo;

    public function __construct(Zibo $zibo) {
    	$this->zibo = $zibo;
    }

    public function createListener($name) {
    	if (String::isEmpty($name)) {
    		throw new ZiboException('Provided name is empty');
    	}

        $configBase = Module::CONFIG_LOG . Config::TOKEN_SEPARATOR . $name . Config::TOKEN_SEPARATOR;

        $listenerClass = $this->getListenerClass($name, $configBase);
        $createCallback = array($listenerClass, Module::METHOD_CREATE_LISTENER);
        $listener = call_user_func($createCallback, $this->zibo, $name, $configBase);

        return $listener;
    }

    private function getListenerClass($name, $configBase) {
        $listeners = $this->getListeners();

        $configType = $configBase . Module::CONFIG_TYPE;
        $type = $this->zibo->getConfigValue($configType);
        if ($type === null) {
            throw new ZiboException('No type provided for log ' . $name);
        }
        if (!isset($listeners[$type])) {
            throw new ZiboException('No listener defined for type ' . $type);
        }
        $class = $listeners[$type];

        try {
            $reflectionClass = new ReflectionClass($class);
            if (!$reflectionClass->implementsInterface(Module::CLASS_LISTENER)) {
                throw new ZiboException($class . ' does not implement ' . Module::CLASS_LISTENER);
            }
        } catch (Exception $e) {
            throw new ZiboException('Could not initiate listener ' . $type, 0, $e);
        }

        return $class;
    }

    private function getListeners() {
        if ($this->listeners !== null) {
        	return $this->listeners;
        }

        $listenerConfig = Module::CONFIG_LOG . Config::TOKEN_SEPARATOR . Module::CONFIG_LISTENER;
        $this->listeners = $this->zibo->getConfigValue($listenerConfig);

        return $this->listeners;
    }

}