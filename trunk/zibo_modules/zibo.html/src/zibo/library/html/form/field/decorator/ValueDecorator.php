<?php

namespace zibo\library\html\form\field\decorator;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Generic decorator for a scalar value, an object value or an array value
 */
class ValueDecorator implements Decorator {

    /**
     * Field name for objects or arrays passed to this decorator
     * @var string
     */
    protected $fieldName;

    /**
     * Constructs a new decorator
     * @param string $fieldName field name for objects or arrays passed to this decorator (optional)
     * @return null
     * @throws zibo\ZiboException when an invalid field name is provided
     */
    public function __construct($fieldName = null) {
        $this->setFieldName($fieldName);
    }

    /**
     * Sets the field name for objects or arrays passed to this decorator
     * @param string $fieldName
     * @return null
     * @throws zibo\ZiboException when the field name is empty or an object
     */
    protected function setFieldName($fieldName) {
        if ($fieldName != null && String::isEmpty($fieldName)) {
            throw new ZiboException('Provided field name is empty');
        }

        $this->fieldName = $fieldName;
    }

    /**
     * Gets the value to decorate, passes it through the decorateValue method and sets the result
     * back to the cell.
     * @param mixed $value Value to decorate
     * @return mixed Decorated value
     */
    public function decorate($value) {
        $value = $this->getValue($value);

        return $this->decorateValue($value);
    }

    /**
     * Performs the actual decorating on the provided value.
     * @param mixed $value The value to decorate
     * @return mixed The decorated value. This implementation will return the value for scalar values,
     * the class name for objects and 'Array' for arrays.
     */
    protected function decorateValue($value) {
        if (is_scalar($value)) {
            return $value;
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return $value->__toString();
            } else {
                return get_class($value);
            }
        }

        if (is_array($value)) {
            $items = array();

            foreach ($value as $item) {
                $item = $this->decorateValue($item);
                if ($item) {
                    $items[] = $item;
                }
            }

            return implode(', ', $items);
        }

        return null;
    }

    /**
     * Gets the value, depending on the value type and the field name set to this decorator.
     * @param mixed $value Value to decorate
     * @return mixed Value of the set field name, or the provided value if no field name set
     */
    protected function getValue($value) {
        if (!$this->fieldName) {
            return $value;
        }

        if (is_object($value)) {
            $fieldName = $this->fieldName;
            if (isset($value->$fieldName)) {
                return $value->$fieldName;
            }

            return null;
        }

        if (is_array($value)) {
            if (array_key_exists($this->fieldName, $value)) {
                return $value[$this->fieldName];
            }
        }

        return null;
    }

}