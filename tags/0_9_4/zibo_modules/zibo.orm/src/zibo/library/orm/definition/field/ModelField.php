<?php

namespace zibo\library\orm\definition\field;

use zibo\library\database\definition\Field;
use zibo\library\orm\definition\FieldValidator;
use zibo\library\orm\exception\OrmException;
use zibo\library\Boolean;
use zibo\library\String;

/**
 * Base field definition for a model table
 */
abstract class ModelField extends Field {

    /**
     * Regular expression for the model name
     * @var string
     */
    const REGEX_NAME = '/^([a-zA-Z0-9]){3,}$/';

    /**
     * Translation key for the name of this field
     * @var string
     */
    protected $label;

    /**
     * flag to see if this field is localized
     * @var boolean
     */
    protected $isLocalized = false;

    /**
     * Definitions of the validators for this field
     * @var array
     */
    protected $validators = array();

    /**
     * Returns the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = parent::__sleep();

        if ($this->label) {
            $fields[] = 'label';
        }

        if ($this->isLocalized) {
            $fields[] = 'isLocalized';
        }

        if ($this->validators) {
            $fields[] = 'validators';
        }

        return $fields;
    }

    /**
     * Reinitialize this object after unserializing
     * @return null
     */
    public function __wakeup() {
        if (!$this->validators) {
            $this->validators = array();
        }
    }

    /**
     * Sets the label for this field
     * @param string $label Translation key for the name of this field
     * @return null
     */
    public function setLabel($label) {
        if ($label !== null && String::isEmpty($label)) {
            throw new OrmException('Provided label is empty');
        }
        $this->label = $label;
    }

    /**
     * Gets the label of this field
     * @return string Translation key for the name of this field
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Set whether this field is localized or not
     * @param boolean $isLocalized
     * @return null
     */
    public function setIsLocalized($isLocalized) {
        $this->isLocalized = Boolean::getBoolean($isLocalized);
    }

    /**
     * Check whether this field is localized
     * @return boolean
     */
    public function isLocalized() {
        return $this->isLocalized;
    }

    /**
     * Add a validator definition to this field
     * @param zibo\library\orm\definition\FieldValidator $validator validator definition
     * @return null
     */
    public function addValidator(FieldValidator $validator) {
        $this->validators[] = $validator;
    }

    /**
     * Get the validator definitions of this field
     * @return array Array containing FieldValidator objects
     */
    public function getValidators() {
        return $this->validators;
    }

}