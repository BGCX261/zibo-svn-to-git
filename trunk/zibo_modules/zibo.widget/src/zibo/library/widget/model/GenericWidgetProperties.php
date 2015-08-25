<?php

namespace zibo\library\widget\model;

/**
 * Generic implementation of WidgetProperties
 */
class GenericWidgetProperties implements WidgetProperties {

    /**
     * The properties data
     * @var array
     */
    private $properties = array();

    /**
     * Sets a property for the widget
     * @param string $key Key of the property
     * @param mixed $value Value of the property
     * @return null
     */
    public function setWidgetProperty($key, $value = null) {
        if ($value !== null) {
            $this->properties[$key] = $value;
            return;
        }

        if (isset($this->properties[$key])) {
            unset($this->properties[$key]);
        }
    }

    /**
     * Gets a property of the widget
     * @param string $key Key of the propery
     * @param mixed $default Default value for when the propery is not set
     * @return mixed The value of the property or the provided default value when the property is not set
     */
    public function getWidgetProperty($key, $default = null) {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }

        return $default;
    }

    /**
     * Clears all the properties of the widget
     * @return null
     */
    public function clearWidgetProperties() {
        $this->properties = array();
    }

}