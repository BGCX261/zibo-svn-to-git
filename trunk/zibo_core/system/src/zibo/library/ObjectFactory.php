<?php

namespace zibo\library;

use zibo\ZiboException;

use \ReflectionClass;
use \ReflectionException;
use \Exception;

/**
 * Create objects on the fly by their class name and optional class interface
 * (implements or extends)
 */
class ObjectFactory {

    /**
     * Initializes a instance of the provided class
     * @param string $class Full name of the class
     * @param string $neededClass Full name of the interface or parent class
     * @param null|array $arguments The arguments for the constructor
     * @return mixed New instance of the requested class
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

}