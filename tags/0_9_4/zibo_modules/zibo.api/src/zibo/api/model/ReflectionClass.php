<?php

namespace zibo\api\model;

use \ReflectionClass as PhpReflectionClass;

/**
 * Overriden the ReflectionClass to get more usefull output for API documentation
 */
class ReflectionClass extends PhpReflectionClass {

    /**
     * API documentation object for this class
     * @var zibo\api\model\doc\Doc
     */
    private $doc;

    /**
     * Get the API documentation object for this class
     * @return zibo\api\model\doc\Doc
     */
    public function getDoc() {
        if ($this->doc) {
            return $this->doc;
        }

        $docParser = ApiBrowser::getDocParser();
        $doc = $this->getDocComment();

        $this->doc = $docParser->parse($doc);

        return $this->doc;
    }

    /**
     * Get the type of this class as a string
     * @return string
     */
    public function getTypeString() {
        $string = '';

        if ($this->isFinal()) {
            $string .= 'final ';
        }

        if ($this->isInterface()) {
            $string .= 'interface';
        } else {
            if ($this->isAbstract()) {
                $string .= 'abstract ';
            }
            $string .= 'class';
        }

        return $string;
    }

    /**
     * Get the inheritance of the class and the methods
     * @return array Array with the class name as key and an array with the methods defined in that
     * class as value and the method name as key.
     */
    public function getInheritance() {
        $inheritance = array();

        $parents = array_reverse($this->getParentArray());
        $methods = $this->getMethods();

        foreach ($parents as $parent) {
            $inheritance[$parent] = array();

            foreach ($methods as $methodName => $method) {
                if (!$method->isInherited($parent)) {
                    continue;
                }
                $inheritance[$parent][$methodName] = $method;
                unset($methods[$methodName]);
            }

            ksort($inheritance[$parent]);
        }
        $inheritance[$this->getName()] = $methods;

        return $inheritance;
    }

    /**
     * Get an array of the class inheritance
     * @param array $parents argument for recursive calls
     * @return array Array with the parent class names
     */
    private function getParentArray(array $parents = array()) {
        $parent = (array) $this->getParentClass();
        if (isset($parent['name'])) {
            $parents[] = $parent['name'];
            $class = new self($parent['name']);
            return $class->getParentArray($parents);
        }

        return $parents;
    }

    /**
     * Get the interface for a method
     * @param string $methodName name of the method
     * @return boolean|string a class name if the method is implemented from a interface, false otherwise
     */
    public function getMethodInterface($methodName) {
        $interfaces = $this->getInterfaceNames();

        foreach ($interfaces as $interface) {
            $interfaceReflection = new self($interface);
            if ($interfaceReflection->hasMethod($methodName)) {
                return $interface;
            }
        }

        return false;
    }

    /**
     * Get the methods of this class
     * @param long $filter not implemented
     * @return array Array containing the method name as the key and a ReflectionMethod object as the value
     */
    public function getMethods($filter = null) {
        $methods = array();

        $classMethods = parent::getMethods();
        foreach ($classMethods as $method) {
            $method = new ReflectionMethod($this->getName(), $method->getName());
            $methods[$method->getName()] = $method;
        }

        ksort($methods);

        return $methods;
    }

    /**
     * Get the properties of this class
     * @param long $filter ReflectionProperty::IS_STATIC, ReflectionProperty::IS_PUBLIC,
     * ReflectionProperty::IS_PROTECTED, or ReflectionProperty::IS_PRIVATE as a way to limit
     * the returned properties.  Flags may be combined by adding them together.
     * @return array Array containing the property name as the key and a ReflectionProperty object as the value
     */
    public function getProperties($filter = null) {
        if ($filter === null) {
            $filter = ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED;
        }

        $properties = array();

        $classProperties = parent::getProperties($filter);
        foreach ($classProperties as $property) {
            $property = new ReflectionProperty($this->getName(), $property->getName());
            $properties[$property->getName()] = $property;
        }

        ksort($properties);

        return $properties;
    }

    /**
     * Get the constants ordered by their name. PHP's reflection interface has no doc comment support for constants.
     * @return array Array with the constant name as the key and the constant value as the value
     */
    public function getConstants() {
        $constants = parent::getConstants();

        ksort($constants);

        return $constants;
    }

}