<?php

/**
 * @package zibo-library
 */
namespace zibo\library;

use zibo\core\Zibo;

use zibo\ZiboException;

use \ReflectionClass;
use \ReflectionException;
use \Exception;

/**
 * Create objects on the fly by their class name and optional class interface (implements or extends)
 */
class ObjectFactory {

    /**
     *
     * @param string $class
     * @param string $neededClass
     * @param null|array $arguments
     */
    public function create($class, $neededClass = null, array $arguments = null) {
        try {
            $classReflection = new ReflectionClass($class);
        } catch (Exception $e) {
            throw new ZiboException('Class ' . $class . ' not found', 0, $e);
        }
        if ($neededClass != null && $class != $neededClass) {
            try {
                $neededClassReflection = new ReflectionClass($neededClass);
            } catch (Exception $e) {
                throw new ZiboException('Needed class ' . $neededClass . ' not found', 0, $e);
            }
            if ($neededClassReflection->isInterface() && !$classReflection->implementsInterface($neededClass)) {
                throw new ZiboException($class . ' does not implement ' . $neededClass);
            } else if (!$classReflection->isSubclassOf($neededClass)) {
                throw new ZiboException($class . ' does not extend ' . $neededClass);
            }
        }

        if (is_null($arguments)) {
            return $classReflection->newInstance();
        }
        return $classReflection->newInstanceArgs($arguments);
    }

    /**
     *
     * @param string $configKey
     * @param string $defaultClass
     * @param string $neededClass
     * @param null|array $arguments
     */
    public function createFromConfig($configKey, $defaultClass, $neededClass, array $arguments = null) {
        $class = Zibo::getInstance()->getConfigValue($configKey, $defaultClass);
        return $this->create($class, $neededClass, $arguments);
    }
}