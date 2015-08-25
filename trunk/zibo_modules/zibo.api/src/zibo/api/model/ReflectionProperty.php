<?php

namespace zibo\api\model;

use \ReflectionProperty as PhpReflectionProperty;

/**
 * Overriden the ReflectionProperty to get more usefull output for API documentation
 */
class ReflectionProperty extends PhpReflectionProperty {

    /**
     * API documentation object for this property
     * @var zibo\api\model\doc\Doc
     */
    private $doc;

    /**
     * Get the documentation object for this property
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
     * Get the type of this property as a string
     * @return string
     */
    public function getTypeString() {
        $string = '';

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

}