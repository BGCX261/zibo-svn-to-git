<?php

namespace zibo\library\widget\model;

/**
 * Data container for the properties of a widget
 */
interface WidgetProperties {

    /**
     * Sets a property for the widget
     * @param string $key Key of the property
     * @param mixed $value Value of the property
     * @return null
     */
    public function setWidgetProperty($key, $value = null);

    /**
     * Gets a property of the widget
     * @param string $key Key of the propery
     * @param mixed $default Default value for when the propery is not set
     * @return mixed The value of the property or the provided default value when the property is not set
     */
    public function getWidgetProperty($key, $default = null);

    /**
     * Clears all the properties of the widget
     * @return null
     */
    public function clearWidgetProperties();

}