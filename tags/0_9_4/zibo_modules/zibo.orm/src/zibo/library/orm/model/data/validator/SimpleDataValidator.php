<?php

namespace zibo\library\orm\model\data\validator;

use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\Validator;
use zibo\library\validation\ValidationFactory;

use zibo\ZiboException;

/**
 * Validator for model data of a model
 */
class SimpleDataValidator extends AbstractDataValidator {

    /**
     * Cosntruct this data validator
     * @param zibo\library\orm\model\meta\ModelMeta $meta the meta of the model
     * @return null
     */
    public function __construct(ModelMeta $meta) {
        parent::__construct();
        $this->initializeValidators($meta);
    }

    /**
     * Validates a data object of a model
     * @param zibo\library\validation\exception\ValidationException $exception the exception where the validation errors will be assigned to
     * @param mixed $data the data container
     * @return null
     */
    public function validateData(ValidationException $exception, $data) {
        foreach ($this->validators as $fieldName => $null) {
            if (!isset($data->$fieldName)) {
                continue;
            }

            $this->validateField($exception, $fieldName, $data->$fieldName);
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
        $errors = array();

        if (!isset($this->validators[$fieldName])) {
            return;
        }

        foreach ($this->validators[$fieldName] as $validator) {
            if (!$validator->isValid($value)) {
                $errors = array_merge($errors, $validator->getErrors());
            }
        }

        if ($errors) {
            $exception->addErrors($fieldName, $errors);
        }
    }

    /**
     * Load the needed validators for this model to this object
     * @param zibo\library\orm\model\meta\ModelMeta $meta the meta of the model
     * @return null
     */
    private function initializeValidators(ModelMeta $meta) {
        $validationFactory = ValidationFactory::getInstance();

        $fields = $meta->getFields();
        foreach ($fields as $fieldName => $field) {
            $fieldValidators = $field->getValidators();
            if (!$fieldValidators) {
                continue;
            }

            $this->validators[$fieldName] = array();
            foreach ($fieldValidators as $fieldValidator) {
                $validator = $validationFactory->createValidator($fieldValidator->getName(), $fieldValidator->getOptions());
                $this->validators[$fieldName][] = $validator;
            }
        }
    }

}