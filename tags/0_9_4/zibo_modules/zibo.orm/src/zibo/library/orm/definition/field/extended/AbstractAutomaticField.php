<?php

namespace zibo\library\orm\definition\field\extended;

use zibo\library\orm\model\ExtendedModel;
use zibo\library\validation\exception\ValidationException;

/**
 * Abstract implementation of the AutomaticField
 */
abstract class AbstractAutomaticField implements AutomaticField {

    /**
     * The model which contains this field
     * @var zibo\library\orm\ExtendedModel
     */
    protected $model;

    /**
     * Construct this automatic field
     * @param zibo\library\orm\ExtendedModel $model
     * @return null
     */
    public function __construct(ExtendedModel $model) {
        $this->model = $model;
    }

    /**
     * Hook to set the default value for this automatic field to the data
     * @param mixed $data data object of the model
     * @return null
     */
    public function createData($data) {

    }

    /**
     * Validate the automatic field
     * @param zibo\library\validation\exception\ValidationException $validationException exception to add possible validation errors
     * @param mixed $data data object of the model
     * @return null
     */
    public function validateData(ValidationException $validationException, $data) {

    }

    /**
     * Hook to perform extra actions when saving a field of the data
     * @param mixed $data data object of the model which is to be saved
     * @param string $fieldName name of the field which is to be saved
     * @param int $id primary key of the data
     * @return null
     */
    public function processSaveField($data, $fieldName, $id) {

    }

    /**
     * Hook to perform extra actions when saving the data
     * @param mixed $data data object of the model which is to be saved
     * @return null
     */
    public function processSaveData($data) {

    }

    /**
     * Hook to perform extra actions when deleting the data
     * @param mixed $data data obejct of the model which is to be deleted
     * @return null
     */
    public function processDeleteData($data) {

    }

}