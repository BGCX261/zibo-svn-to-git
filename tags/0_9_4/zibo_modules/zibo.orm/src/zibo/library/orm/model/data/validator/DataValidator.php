<?php

namespace zibo\library\orm\model\data\validator;

use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\Validator;

/**
 * Interface for a validator for model data
 */
interface DataValidator {

    /**
     * Adds a custom validator for a specified field
     * @param string $fieldName Name of the field
     * @param zibo\library\validation\validator\Validator $validator The validator for the field
     * @return null
     */
    public function addValidator($fieldName, Validator $validator);

    /**
     * Validates a data object of a model
     * @param zibo\library\validation\exception\ValidationException $exception the exception where the validation errors will be assigned to
     * @param mixed $data the data container
     * @return null
     */
    public function validateData(ValidationException $exception, $data);

    /**
     * Validates the field of a data object of a model
     * @param zibo\library\validation\exception\ValidationException $exception the exception where the validation errors will be assigned to
     * @param string $fieldName name of the field
     * @param mixed $value value to be validated
     * @return null
     */
    public function validateField(ValidationException $exception, $fieldName, $value);

}