<?php

namespace zibo\library\orm\definition;

use zibo\library\orm\exception\OrmException;
use zibo\library\String;

/**
 * Validator definition for a ModelField
 */
class FieldValidator {

    /**
     * Name of the validator, as defined in the validation configuration
     * @var string
     */
    private $name;

    /**
     * Options for the validator
     * @var array
     */
    private $options;

    /**
     * Construct this validator definition
     * @param string $name name of the validator, as defined in the validation configuration
     * @param array $options options for the validator
     * @return null
     * @throws zibo\ZiboException when $name is not a string
     * @throws zibo\library\orm\exception\ModelException when $name is empty
     */
    public function __construct($name, array $options = array()) {
        $this->setName($name);
        $this->setOptions($options);
    }

    /**
     * Get the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = array('name');

        if ($this->options) {
            $fields[] = 'options';
        }

        return $fields;
    }

    /**
     * Reinitialize the field validator after unserializing
     * @return null
     */
    public function __wakeup() {
        if (!$this->options) {
            $this->options = array();
        }
    }

    /**
     * Set the name of the validator
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when $name is not a string
     * @throws zibo\library\orm\exception\ModelException when $name is empty
     */
    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new OrmException('Name is empty');
        }

        $this->name = $name;
    }

    /**
     * Get the name of the validator
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the options for the validator
     * @param array $options
     * @return null
     */
    private function setOptions(array $options) {
        $this->options = $options;
    }

    /**
     * Get the options for the validator
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Checks if the provided validator is the same as this one
     * @param FieldValidator $validator
     * @return boolean True if the validator is the same, false otherwise
     */
    public function equals(FieldValidator $validator) {
        if ($this->name != $validator->getName()) {
            return false;
        }

        $options = $validator->getOptions();

        if (count($options) != count($this->options)) {
            return false;
        }

        foreach ($this->options as $key => $value) {
            if (!array_key_exists($key, $options)) {
                return false;
            }

            if ($value != $options[$key]) {
                return false;
            }
        }

        return true;
    }

}