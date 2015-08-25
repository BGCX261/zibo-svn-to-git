<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\ZiboException;

/**
 * Abstract Validator implementation with basic error handling functions
 */
abstract class AbstractValidator implements Validator {

    /**
     * Array with the ValidationError objects
     * @var array
     */
    protected $errors;

    /**
     * Construct a new validator instance
     * @param array $options options for the validator
     * @return null
     */
    public function __construct(array $options = array()) {
        $this->errors = array();
    }

    /**
     * Add an error to this validator
     * @param string $code
     * @param string $message
     * @param string $parameters
     * @return null
     */
    protected function addValidationError($code, $message, $parameters) {
        $error = new ValidationError($code, $message, $parameters);
        $this->addError($error);
    }

    /**
     * Add an error to this validator
     * @param zibo\library\validation\ValidationError $error
     * @return null
     */
    protected function addError(ValidationError $error) {
        $this->errors[] = $error;
    }

    /**
     * Get the errors of the last isValid call
     * @return array Array with zibo\library\validation\ValidationError objects
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Clear the errors
     * @return null
     */
    protected function resetErrors() {
        $this->errors = array();
    }

}