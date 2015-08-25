<?php

/**
 * @package zibo-log-filter
 */
namespace zibo\log\filter;

use zibo\core\Zibo;

use zibo\library\config\Config;
use zibo\library\String;

use zibo\log\Module;

use zibo\ZiboException;

use \Exception;
use \ReflectionClass;
use \ReflectionException;

class LogItemFilterFactory {

	private $filters;
	private $zibo;

	public function __construct(Zibo $zibo) {
		$this->zibo = $zibo;
	}

    public function createFilter($name, $configBase) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        $filterClass = $this->getFilterClass($name, $configBase);
        $createCallback = array($filterClass, Module::METHOD_CREATE_FILTER);
        $listener = call_user_func($createCallback, $this->zibo, $name, $configBase);

        return $listener;
    }

    private function getFilterClass($name, $configBase) {
        $filters = $this->getFilters();

        $configType = $configBase . Module::CONFIG_TYPE;
        $type = $this->zibo->getConfigValue($configType);
        if ($type === null) {
            throw new ZiboException('No type provided for filter ' . $name);
        }
        if (!isset($filters[$type])) {
            throw new ZiboException('No filter defined for type ' . $type);
        }
        $class = $filters[$type];

        try {
            $reflectionClass = new ReflectionClass($class);
            if (!$reflectionClass->implementsInterface(Module::CLASS_FILTER)) {
                throw new ZiboException($class . ' does not implement ' . Module::CLASS_FILTER);
            }
        } catch (Exception $e) {
            throw new ZiboException('Could not initiate filter ' . $type, 0, $e);
        }

        return $class;
    }

    private function getFilters() {
        if ($this->filters !== null) {
            return $this->filters;
        }

        $filterConfig = Module::CONFIG_LOG . Config::TOKEN_SEPARATOR . Module::CONFIG_FILTER;
        $this->filters = $this->zibo->getConfigValue($filterConfig);

        return $this->filters;
    }

}