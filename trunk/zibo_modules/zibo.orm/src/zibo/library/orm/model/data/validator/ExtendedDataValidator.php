<?php

namespace zibo\library\orm\model\data\validator;

use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\Validator;

/**
 * Validator for model data of a extended model
 */
class ExtendedDataValidator implements DataValidator {

    /**
     * The automatic fields of a extended model
     * @var array
     */
    private $automaticFields;

    /**
     * Extra validator
     * @var DataValidator
     */
    private $dataValidator;

    /**
     * Cosntruct this data validator
     * @param array $automaticFields
     * @param DataValidator $dataValidator
     * @return null
     */
    public function __construct(array $automaticFields = null, DataValidator $dataValidator = null) {
        $this->automaticFields = $automaticFields;
        $this->dataValidator = $dataValidator;
    }

    /**
     * Adds a custom validator for a specified field
     * @param string $fieldName Name of the field
     * @param zibo\library\validation\validator\Validator $validator The validator for the field
     * @return null
     */
    public function addValidator($fieldName, Validator $validator) {
        $this->dataValidator->addValidator($fieldName, $validator);
    }

    /**
     * Validates a data object of a model
     * @param zibo\library\validation\exception\ValidationException $exception the exception where the validation errors will be assigned to
     * @param mixed $data the data container
     * @return null
     */
    public function validateData(ValidationException $exception, $data) {
        $this->dataValidator->validateData($exception, $data);

        if (!$this->automaticFields) {
            return;
        }

        foreach ($this->automaticFields as $field) {
            $field->validateData($exception, $data);
        }
    }

    /**
     * Validates the field of a data object of a model
     * @param zibo\library\validation\exception\ValidationException $exception the exception where the validation errors will be assigned to
     * @param string $fieldName name of the field
     * @param mixed $value value to be validated
     * @return null
     */
    public function validateField(ValidationException $exception, $fieldName, $value) {
        $this->dataValidator->validateField($exception, $fieldName, $value);
    }

}