<?php

namespace zibo\library\validation\validator;

/**
 * Validator interface
 */
interface Validator {

    /**
     * Construct a new validator instance
     * @param array $options options for this instance
     * @return null
     */
    public function __construct(array $options = null);

    /**
     * Check if a value is valid
     * @param mixed $value value to check
     * @return boolean true when the value is valid, false otherwise
     */
    public function isValid($value);

    /**
     * Get the errors of the last isValid call
     * @return array Array with zibo\library\validation\ValidationError objects
     */
    public function getErrors();

}