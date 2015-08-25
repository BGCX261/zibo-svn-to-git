<?php

namespace zibo\api\model;

use \ReflectionMethod as PhpReflectionMethod;

/**
 * Overriden the ReflectionMethod to get more usefull output for API documentation
 */
class ReflectionMethod extends PhpReflectionMethod {

    /**
     * Name of the class which holds this method
     * @var string
     */
    private $className;

    /**
     * API documentation object for this method
     * @var zibo\api\model\doc\Doc
     */
    private $doc;

    /**
     * Construct a new reflection method object
     * @param mixed $classOrName
     * @param string $name name of the class
     * @return null
     */
    public function __construct ($classOrName = null, $name = null) {
        parent::__construct($classOrName, $name);
        if ($name) {
            $this->className = $classOrName;
        }
    }

    /**
     * Get the API documentation object for this method
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
     * Get the type of this method as a string
     * @return string
     */
    public function getTypeString() {
        $string = '';
        if ($this->isFinal()) {
            $string = 'final ';
        } else if ($this->isAbstract()) {
            $string = 'abstract ';
        }

        if ($this->isPublic()) {
            $string .= 'public';
        } elseif ($this->isProtected()) {
            $string .= 'protected';
        } elseif ($this->isPrivate()) {
            $string .= 'private';
        }

        if ($this->isStatic()) {
            $string .= ' static';
        }

        return $string;
    }

    /**
     * Check whether this method is inherited
     * @param string $className check if this method is inherited from this provided class instead of generally inherited
     * @return boolean true if this method is inherited
     */
    public function isInherited($className = null) {
        if ($className === null) {
            $className = $this->className;
        }

        $declaringClass = $this->getDeclaringClass();
        $declaringClassName = $declaringClass->getName();

        return $declaringClassName === $className;
    }

}