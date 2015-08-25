<?php

namespace zibo\library\orm\model;

use zibo\library\orm\definition\field\extended\AutomaticField;
use zibo\library\orm\definition\field\extended\DateAddedField;
use zibo\library\orm\definition\field\extended\DateModifiedField;
use zibo\library\orm\definition\field\extended\VersionField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\model\data\validator\ExtendedDataValidator;
use zibo\library\validation\exception\ValidationException;

/**
 * Data model with automated fields
 */
class ExtendedModel extends SimpleModel {

    /**
     * Array with the automatic fields
     * @var array
     */
    private $automaticFields;

    /**
     * Initializes a new extended data model
     * @return null
     */
    protected function initialize() {
        parent::initialize();

        $this->automaticFields = array();

        $this->addAutomaticField(new DateAddedField($this));
        $this->addAutomaticField(new DateModifiedField($this));
        $this->addAutomaticField(new VersionField($this));

        $this->dataValidator = new ExtendedDataValidator($this->automaticFields, $this->dataValidator);
    }

    /**
     * Serializes this model
     * @return string
     */
    public function serialize() {
        $serializeArray = array(
            'automaticFields' => $this->automaticFields,
            'model' => parent::serialize(),
        );

        return serialize($serializeArray);
    }

    /**
     * Unserializes the provided string into a model
     * @param string $serialized Serialized string of a model
     * @return null
     */
    public function unserialize($serialized) {
        $serializeArray = unserialize($serialized);

        $this->automaticFields = $serializeArray['automaticFields'];

        parent::unserialize($serializeArray['model']);

        $this->dataValidator = new ExtendedDataValidator($this->automaticFields, $this->dataValidator);
    }

    /**
     * Add an automatic field to the model. Only fields defined in the model table will be added
     * @param zibo\library\orm\definition\field\extended\AutomaticField $field
     * @return boolean True if the field has been added, false otherwise
     */
    protected function addAutomaticField(AutomaticField $field) {
        $fieldName = $field->getName();

        if (!$this->meta->hasField($fieldName)) {
            return false;
        }

        $this->automaticFields[$fieldName] = $field;

        return true;
    }

    /**
     * Get the names of the automatic fields
     * @return array Array with the names of the automatic fields
     */
    public function getAutomaticFields() {
        return array_keys($this->automaticFields);
    }

    /**
     * Create a new data object for this model
     * @param boolean $initialize true to create a data object with default values (default), false to create an empty data object
     * @return mixed a new data object for this model
     */
    public function createData($initialize = true) {
        $data = parent::createData($initialize);

        if ($initialize) {
            foreach ($this->automaticFields as $field) {
                $field->createData($data);
            }
        }

        return $data;
    }

    /**
     * Saves a field from data to the model
     * @param mixed $data A data object or the value to save when the id argument is provided
     * @param string $fieldName Name of the field to save
     * @param integer $id Primary key of the data to save, $data will be considered as the value
     * @param string $locale The locale of the data, only used when the id argument is provided
     * @return null
     * @throws Exception when the field could not be saved
     */
    protected function saveField($data, $fieldName, $id = null, $locale = null) {
        parent::saveField($data, $fieldName, $id, $locale);

        foreach ($this->automaticFields as $field) {
            $field->processSaveField($data, $fieldName, $id, $locale);
        }
    }

    /**
     * Saves a data object to the model
     * @param mixed $data A data object of this model
     * @return null
     * @throws Exception when the data could not be saved
     */
    protected function saveData($data) {
        foreach ($this->automaticFields as $field) {
            $field->processSaveData($data);
        }

        parent::saveData($data);
    }

    /**
     * Deletes data from this model
     * @param mixed $data Primary key of the data or a data object of this model
     * @return null
     */
    protected function deleteData($data) {
        $data = parent::deleteData($data);

        foreach ($this->automaticFields as $field) {
            $field->processDeleteData($data);
        }

        return $data;
    }

}