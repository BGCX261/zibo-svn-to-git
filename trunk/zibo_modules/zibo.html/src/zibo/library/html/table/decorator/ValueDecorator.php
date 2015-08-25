<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
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
     * @param string|array $fieldName Name of the propery, or an array with recursive properties, for the table objects or arrays passed to this decorator (optional)
     * @return null
     * @throws zibo\ZiboException when an invalid field name is provided
     * @see getFieldValue
     */
    public function __construct($fieldName = null) {
        $this->setFieldName($fieldName);
    }

    /**
     * Sets the field name for objects or arrays passed to this decorator
     * @param string|array $fieldName
     * @return null
     * @throws zibo\ZiboException when the field name is empty or an object
     */
    protected function setFieldName($fieldName) {
        if (is_array($fieldName)) {
            foreach ($fieldName as $index => $token) {
                if (!String::isEmpty($token)) {
                    continue;
                }

                throw new ZiboException('Token with index ' . $index . ' of the field name array is empty');
            }
        } elseif ($fieldName != null && String::isEmpty($fieldName)) {
            throw new ZiboException('Provided field name is empty');
        }

        $this->fieldName = $fieldName;
    }

    /**
     * Gets the value from the cell, passes it through the decorateValue method and sets the result
     * back to the cell.
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $value = $this->getValue($cell);

        $value = $this->decorateValue($value);

        $cell->setValue($value);
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
     * Gets the value from the provided cell, depending on the value type and the field name
     * set to this decorator.
     * @param zibo\library\html\table\Cell $cell
     * @return mixed Value of the cell
     */
    protected function getValue(Cell $cell) {
        $value = $cell->getValue();

        if (!$this->fieldName) {
            return $value;
        }

        if (!is_array($this->fieldName)) {
            return $this->getFieldValue($value, $this->fieldName);
        }

        foreach ($this->fieldName as $fieldName) {
            $value = $this->getFieldValue($value, $fieldName);

            if ($value === null) {
                break;
            }
        }

        return $value;
    }

    /**
     * Gets the property with the provided field name of the provided value
     * @param mixed $value The original value to retrieve the property from
     * @param string|array $fieldName Name of the property or an array with recursive properties
     *                                eg. Value is a object value
     *                                {
     *                                    'name': 'Doe',
     *                                    'firstname': 'John',
     *                                    'parameters': array(
     *                                        'param1': 'value1',
     *                                        'param2': 'value2'
     *                                     )
     *                                }
     *
     *                                If the provided $fieldName is 'name', the result will be 'Doe',
     *                                if the provided $fieldName is array('parameters', 'param2'), the result will be 'value2'
     * @return mixed
     */
    protected function getFieldValue($value, $fieldName) {
        if (is_array($value)) {
            if (array_key_exists($fieldName, $value)) {
                return $value[$fieldName];
            }

            return null;
        }

        if (is_object($value)) {
            if (isset($value->$fieldName)) {
                return $value->$fieldName;
            }
        }

        return null;
    }

}