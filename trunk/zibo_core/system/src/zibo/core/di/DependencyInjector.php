<?php

namespace zibo\core\di;

use zibo\library\Callback;
use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Implementation of a dependency injector. Load class instances dynamically
 * from a dependency container when and only when needed.
 */
class DependencyInjector {

    /**
     * Class name of the Zibo core
     * @var string
     */
    const INTERFACE_ZIBO = 'zibo\\core\\Zibo';

    /**
     * Instance of the object factory
     * @var zibo\library\ObjectFactory
     */
    protected static $objectFactory;

    /**
     * Container of the injection dependencies
     * @var DependencyContainer
     */
    protected static $container;

    /**
     * Created dependency instances
     * @var array
     */
    protected static $instances;

    /**
     * Sets the container of the dependencies. All created instances will be reset.
     * @param zibo\core\di\DependencyContainer $container The container to set
     * @return null
     */
    public function setContainer(DependencyContainer $container) {
        self::$container = $container;
        self::$instances = null;
    }

    /**
     * Gets the container of the dependencies
     * @return zibo\core\di\InjectionDefinitionContainer
     */
    public function getContainer() {
        if (self::$container) {
            return self::$container;
        }

        self::$container = new DependencyContainer();

        return self::$container;
    }

    /**
     * Overrides the container by setting an instance which will always be
     * returned by get if the provided object's class name is requested
     * @param object $instance Instance to set
     * @param string $interface Interface to set the instance for, if not provided
     * the class name of the instance will be used as interface
     * @return null
     * @throws zibo\ZiboException if the provided instance is not a object
     * @throws zibo\ZiboException if the provided interface is empty or invalid
     */
    public function setInstance($instance, $interface = null) {
        if (!is_object($instance)) {
            throw new ZiboException('Provided instance is not an object');
        }

        if ($interface !== null) {
            if (!String::isString($interface, String::NOT_EMPTY)) {
                throw new ZiboException('Provided interface is empty or invalid');
            }
        } else {
            $interface = get_class($instance);
        }

        if (!isset(self::$instances)) {
            self::$instances = array($interface => $instance);
        } else {
            self::$instances[$interface] = $instance;
        }
    }

    /**
     * Gets all the defined instances of the provided class
     * @param string $interface The full class name of the interface or parent
     * class
     * @return array
     */
    public function getAll($interface) {
        $interfaceDependencies = array();

        $container = $this->getContainer();
        $dependencies = $container->getDependencies($interface);
        foreach ($dependencies as $dependency) {
            $id = $dependency->getId();
            $interfaceDependencies[$id] = $this->get($interface, $id);
        }

        return $interfaceDependencies;
    }

    /**
     * Gets a defined instance of the provided class
     * @param string $interface The full class name of the interface or parent
     * class
     * @param string $id The id of the dependency to get a specific definition.
     * If an id is provided,the exclude array will be ignored
     * @param array $exclude Array with the interface as key and an array with
     * id's of dependencies as key to exclude from this get call. You should not
     * set this argument, this is used in recursive calls for the actual
     * dependency injection.
     * @return mixed Instance of the requested class
     * @throws zibo\ZiboException if the class name or the id are invalid
     * @throws zibo\ZiboException if the dependency could not be created
     */
    public function get($interface, $id = null, array $exclude = null) {
        if (!String::isString($interface, String::NOT_EMPTY)) {
            throw new ZiboException('Provided class name is empty or invalid');
        }

        if (isset(self::$instances[$interface]) && !is_array(self::$instances[$interface])) {
            // an instance of this interface is manually set, return it
            return self::$instances[$interface];
        }

        $container = $this->getContainer();
        $dependencies = $container->getDependencies($interface);

        $dependency = null;

        if ($id !== null) {
            // gets a specific instance of the provided interface
            if (!String::isString($id, String::NOT_EMPTY)) {
                throw new ZiboException('Provided id of the injection dependency is empty or invalid');
            }

            if (isset(self::$instances[$interface][$id])) {
                // the instance is already created
                return self::$instances[$interface][$id];
            }

            if (!isset($dependencies[$id])) {
                throw new ZiboException('No injection dependency set for the provided id');
            }

            $dependency = $dependencies[$id];
        } else {
            if (isset(self::$instances[$interface])) {
                // already a instance of the interface set
                $instances = array_reverse(self::$instances[$interface]);

                // gets the last created dependency which is not excluded
                do {
                    $instance = each($instances);
                    if (!$instance) {
                        break;
                    }

                    $id = $instance[0];
                    $instance = $instance[1];
                } while (isset($exclude[$interface][$id]));

                if ($instance) {
                    // there is a dependency created which is not excluded
                    return $instance;
                }
            }

            // no instances created or all are excluded, try to create a new one
            if (!$dependencies) {
                throw new ZiboException('No injectable dependency set for ' . $interface);
            }

            // gets the last defined dependency which is not excluded
            do {
                $dependency = array_pop($dependencies);
                if (!$dependency) {
                    throw new ZiboException('No injectable dependency set for ' . $interface);
                }

                $id = $dependency->getId();
            } while (isset($exclude[$interface][$id]));
        }

        // prepare the instance index for this interface
        if (!isset(self::$instances[$interface])) {
            self::$instances[$interface] = array();
        } elseif (!isset(self::$instances)) {
            self::$instances = array($interface => array());
        }

        // creates a new instance, indexes and returns it
        return self::$instances[$interface][$id] = $this->create($interface, $dependency, $exclude);
    }

    /**
     * Creates an instance of the provided dependency
     * @param string $interface Full class name of the interface or parent class
     * @param Dependency $dependency Definition of the class to create
     * @param array $exclude Array with the interface as key and an array with
     * id's of dependencies as key to exclude from the get calls.
     * @return mixed Instance of the dependency
     * @throws zibo\ZiboException when the dependency could not be created
     */
    protected function create($interface, Dependency $dependency, array $exclude = null) {
        if (!self::$objectFactory) {
            self::$objectFactory = new ObjectFactory();
        }

        if (!$exclude) {
            $exclude = array($interface => array($dependency->getId() => true));
        } elseif (!isset($exclude[$interface])) {
            $exclude[$interface] = array($dependency->getId() => true);
        } else {
            $exclude[$interface][$dependency->getId()] = true;
        }

        $className = $dependency->getClassName();
        $arguments = $dependency->getConstructorArguments();
        $arguments = $this->getCallbackArguments($arguments, $exclude);

        $instance = self::$objectFactory->create($className, $interface, $arguments);

        $calls = $dependency->getCalls();
        if ($calls) {
            foreach ($calls as $call) {
                $arguments = $this->getCallbackArguments($call->getArguments(), $exclude);
                $callback = new Callback(array($instance, $call->getMethodName()));
                $callback->invokeWithArrayArguments($arguments);
            }
        }

        return $instance;
    }

    /**
     * Gets the actual values of the provided arguments
     * @param array $arguments Array of dependency call arguments
     * @return array Array with the values of the call arguments
     * @see DependencyCallArgument
     */
    protected function getCallbackArguments(array $arguments = null, array $exclude = null) {
        $callArguments = array();

        if ($arguments === null) {
            return $callArguments;
        }

        foreach ($arguments as $argument) {
            switch ($argument->getType()) {
                case DependencyCallArgument::TYPE_NULL:
                    $callArguments[] = null;
                    break;
                case DependencyCallArgument::TYPE_VALUE:
                    $callArguments[] = $argument->getValue();
                    break;
                case DependencyCallArgument::TYPE_DEPENDENCY:
                    $callArguments[] = $this->get($argument->getValue(), $argument->getDependencyId(), $exclude);
                    break;
                case DependencyCallArgument::TYPE_CONFIG:
                    $zibo = $this->get(self::INTERFACE_ZIBO);
                    $callArguments[] = $zibo->getConfigValue($argument->getValue(), $argument->getDefaultValue());
                    break;
            }
        }

        return $callArguments;
    }

}