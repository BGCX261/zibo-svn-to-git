<?php

namespace zibo\library\html;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Abstract implementation of a HTML element
 */
abstract class AbstractElement implements Element {

    /**
     * Name of the id attribute
     * @var string
     */
    const ATTRIBUTE_ID = 'id';

    /**
     * Name of the class attribute
     * @var string
     */
    const ATTRIBUTE_CLASS = 'class';

    /**
     * The style id of this element
     * @var string
     */
    private $id;

    /**
     * The style classes of this element
     * @var array
     */
    private $class;

    /**
     * The attributes of this element
     * @var array
     */
    private $attributes;

    /**
     * Construct a new element
     * @return null
     */
    public function __construct() {
        $this->id = null;
        $this->class = array();
        $this->attributes = array();
    }

    /**
     * Sets the style id of this element
     * @param string $id
     * @return null
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the style id of this element
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the HTML of the style id attribute
     * @return string
     */
    protected function getIdHtml() {
        if ($this->id) {
            return $this->getAttributeHtml(self::ATTRIBUTE_ID, $this->id);
        }

        return '';
    }

    /**
     * Sets the style class of this element
     * @param string $class
     * @return null
     */
    public function setClass($class) {
        $class = trim($class);

        if (String::isEmpty($class)) {
            $this->class = array();
        } else {
            $this->class = array($class => $class);
        }
    }

    /**
     * Adds a style class to the style class of this element
     * @param string $class
     * @return null
     * @throws zibo\ZiboException when the provided style class is empty
     */
    public function appendToClass($class) {
        $class = trim($class);

        if (String::isEmpty($class)) {
            throw new ZiboException('Provided class is empty');
        }

        $this->class[$class] = $class;
    }

    /**
     * Removes a style class from the style class of this element
     * @param string $class
     * @return null
     * @throws zibo\ZiboException when the provided style class is empty or not a string
     */
    public function removeFromClass($class) {
        if (String::isEmpty($class)) {
            throw new ZiboException('Provided class is empty');
        }

        if (array_key_exists($class, $this->class)) {
            unset($this->class[$class]);
        }
    }

    /**
     * Get the current class(es)
     * @return string
     */
    public function getClass() {
        $result = '';

        foreach ($this->class as $class) {
            $result .= ($result == '' ? '' : ' ' ) . $class;
        }

        return $result;
    }

    /**
     * Get the HTML of the class attribute
     * @return string
     */
    protected function getClassHtml() {
        if ($this->class) {
            return $this->getAttributeHtml(self::ATTRIBUTE_CLASS, $this->getClass());
        }

        return '';
    }

    /**
     * Sets an attribute for this element
     * @param string $attribute name of the attribute
     * @param string $value value of the attribute
     * @return null
     * @throws zibo\ZiboException when the name of attribute is empty or not a string
     */
    public function setAttribute($attribute, $value) {
        if (String::isEmpty($attribute)) {
            throw new ZiboException('Provided name of the attribute is empty');
        }

        if ($attribute == self::ATTRIBUTE_ID) {
            return $this->setId($value);
        }
        if ($attribute == self::ATTRIBUTE_CLASS) {
            return $this->setClass($value);
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * Gets all the attributes of this element
     * @return array Array with the attribute name as key and the attribute value as value
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Gets a attribute of this element
     * @param string $attribute name of the attribute
     * @param mixed $default value to return when the attribute is not set
     * @return string the value of the attribute
     */
    public function getAttribute($attribute, $default = null) {
        if ($attribute == self::ATTRIBUTE_ID) {
            return $this->getId();
        }
        if ($attribute == self::ATTRIBUTE_CLASS) {
            return $this->getClass();
        }

        $result = $default;
        if (array_key_exists($attribute, $this->attributes)) {
            $result = $this->attributes[$attribute];
        }

        return $result;
    }

    /**
     * Clear all the attributes
     * @return null
     */
    public function resetAttributes() {
        $this->class = array();
        $this->attributes = array();
    }

    /**
     * Gets the HTML of the attributes of this element
     * @return string HTML of the attributes
     */
    protected function getAttributesHtml() {
        if (!$this->attributes) {
            return '';
        }

        $result = '';
        foreach ($this->attributes as $attribute => $value) {
            $result .= $this->getAttributeHtml($attribute, $value);
        }

        return $result;
    }

    /**
     * Gets the HTML of a attribute
     * @param string $attribute name of the attribute
     * @param string $value value of the attribute
     * @return string HTML of the attribute (eg. ' name="value"')
     */
    protected function getAttributeHtml($attribute, $value) {
        return ' ' . $attribute . '="' . htmlspecialchars($value) . '"';
    }

}