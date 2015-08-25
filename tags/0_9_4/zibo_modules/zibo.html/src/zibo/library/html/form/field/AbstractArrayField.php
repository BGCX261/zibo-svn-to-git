<?php


namespace zibo\library\html\form\field;

use zibo\library\html\form\field\decorator\Decorator;
use zibo\library\html\AbstractElement;
use zibo\library\String;
use zibo\library\Structure;

/**
 * Abstract form field implementation for array fields
 */
abstract class AbstractArrayField extends AbstractField {

    /**
     * Decorator for the key of a option
     * @var zibo\library\html\form\field\decorator\Decorator
     */
    protected $keyDecorator;

    /**
     * Decorator for the value of a option
     * @var zibo\library\html\form\field\decorator\Decorator
     */
    protected $valueDecorator;

    /**
     * Flag to see if multiple values can be selected
     * @var boolean
     */
    protected $isMultiple;

    /**
     * The options for this field
     * @var array
     */
    protected $options = array();

    /**
     * Value for the empty option
     * @var string
     */
    protected $emptyKey;

    /**
     * Value for the empty option
     * @var string
     */
    protected $emptyValue;

    /**
     * Gets the HTML of the name attribute
     * @param string $option Array key for in the name of the option (only applicable if this field has multiple values)
     * @return string
     */
    protected function getNameHtml($option = null) {
        return ' name="' . $this->name . ($this->isMultiple ? '[' . $option . ']' : '') . '"';
    }

    /**
     * Sets a decorator for the key of the options. This decorator will decorate the value of the option (and not the key) into a new key for the option
     * @param zibo\library\html\form\field\decorator\Decorator $decorator
     * @return null
     */
    public function setKeyDecorator(Decorator $decorator) {
        $this->keyDecorator = $decorator;
    }

    /**
     * Sets a decorator for the value of the options
     * @param zibo\library\html\form\field\decorator\Decorator $decorator
     * @return null
     */
    public function setValueDecorator(Decorator $decorator) {
        $this->valueDecorator = $decorator;
    }

    /**
     * Sets the options for this field
     * @param array $options
     * @return null
     */
    public function setOptions(array $options) {
        $this->options = $options;
    }

    /**
     * Gets the options of this field
     * @return array Array with the options
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Add a empty value to the option
     * @param string $value label of the option
     * @param string $key key of the option
     * @return null
     */
    public function addEmpty($value = '---', $key = 0) {
        $this->emptyValue = $value;
        $this->emptyKey = $key;
    }

    /**
     * Sets whether multiple options can be selected for this field
     * @param boolean $isMultiple
     * @return null
     */
    public function setIsMultiple($isMultiple) {
        $this->isMultiple = $isMultiple;

        if ($isMultiple) {
            if (!$this->defaultValue) {
                $this->defaultValue = array();
            }
        } else {
            if (!$this->defaultValue && is_array($this->defaultValue)) {
                $this->defaultValue = null;
            }
        }
    }

    /**
     * Checks whether multiple options can be selected for this field
     * @return boolean true if multiple options can be selected, false otherwise
     */
    public function isMultiple() {
        return $this->isMultiple;
    }

    /**
     * Process the request and update the value of this field if found in the request
     * @return null
     */
    public function processRequest() {
        $value = $this->getRequestValue();

        if (is_null($value)) {
            if ($this->isMultiple()) {
                $this->setValue(array());
            }
            return;
        }

        if (is_array($value)) {
            $requestValues = $value;
            $value = array();
            foreach ($requestValues as $key => $requestValue) {
                if (!is_array($requestValue) && isset($this->options[$requestValue])) {
                    $value[$requestValue] = $this->options[$requestValue];
                } else {
                    $value[$key] = $requestValue;
                }
            }
        }

        $this->setValue($value);
        $this->applyFilters();
    }

    /**
     * Processes the decorators on the key and value and updates the isSelected boolean based on the provided selected value
     * @param mixed $key Key of the option. Will be updated with a decorated value if a key decorator is set
     * @param mixed $value Value of the option. Will be updated with a decorated value if a value decorator is set
     * @param boolean $isSelected Flag to see if the provided value is a selected value. Will be updated after decorator the values
     * @param mixed $selected The selected value of the field
     * @return null
     */
    protected function processValue(&$key, &$value, &$isSelected, $selected) {
        if ($this->keyDecorator) {
            $key = $this->keyDecorator->decorate($value);
        }
        if ($this->valueDecorator) {
            $value = $this->valueDecorator->decorate($value);
        }

        $isSelected = $this->isValueSelected($key, $value, $selected);
    }

    /**
     * Checks if the provided option is a selected value
     * @param string $key Key of the option
     * @param string $value Value of the option
     * @param mixed $selected The selected value of the field
     * @return boolean True if the provided option is a selected value, false otherwise
     */
    protected function isValueSelected($key, $value, $selected) {
        if (is_array($selected)) {
            $isSelected = isset($selected[$key]);
        } elseif (is_object($selected)) {
            if ($this->keyDecorator) {
                $isSelected = $key == $this->keyDecorator->decorate($selected);
            } else {
                $isSelected = false;
            }
        } else {
            $isSelected = $key == $selected;
        }

        return $isSelected;
    }

}