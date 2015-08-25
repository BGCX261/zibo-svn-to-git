<?php

namespace zibo\library\validation\exception;

use zibo\library\validation\ValidationError;

use zibo\ZiboException;

/**
 * ValidationException containing the ValidationErrors
 */
class ValidationException extends ZiboException {

    /**
     * Array with the errors per field
     * @var array
     */
    private $errors = array();

    /**
     * Construct this exception
     */
    public function __construct($message = 'Validation errors occured') {
        parent::__construct($message);
    }

    /**
     * Check whether this exception contains error
     * @param string $name to check whether there are errors for a certain field, provide the field name. To check for any errors, provide null.
     * @return boolean true if there are errors, false otherwise
     */
    public function hasErrors($name = null) {
        if ($name == null) {
            return !empty($this->errors);
        }

        return isset($this->errors[$name]);
    }

    /**
     * Add errors for a field in this exception
     * @param string $name name of the field
     * @param array $errors Array containing ValidationError instances
     * @return null
     * @throws zibo\ZiboException when a value of the errors array is not a ValidationError instance
     */
    public function addErrors($name, array $errors) {
        foreach ($errors as $error) {
            if (!($error instanceof ValidationError)) {
                throw new ZiboException('Provided error is not a ValidationError');
            }

            if (!isset($this->errors[$name])) {
                $this->errors[$name] = array();
            }

            $this->errors[$name][] = $error;
        }
    }

    /**
     * Get the errors for a field
     * @param string $name name of the field
     * @return array Array with ValidationError instances
     * @throws zibo\ZiboException when no errors set for the field
     */
    public function getErrors($name) {
        if (!$this->hasErrors($name)) {
            throw new ZiboException('No errors set for ' . $name);
        }

        return $this->errors[$name];
    }

    /**
     * Get all the error of this exception
     * @return array Array with the field name as key and an array with ValidationError instances as value
     */
    public function getAllErrors() {
        return $this->errors;
    }

    /**
     * Get all the errors as a html string
     * @return string html representation of the errors in this exception
     */
    public function getErrorsAsString() {
        if (!$this->hasErrors()) {
            return;
        }

        $string = '<ul>';
        foreach ($this->errors as $name => $errors) {
            $string .= '<li>' . $this->getFieldErrorsAsString($name, $errors) . '</li>';
        }
        $string .= '</ul>';

        return $string;
    }

    /**
     * Get the errors for a field as a string
     * @param string $name name of the field
     * @param array $errors Array containing ValidationError objects
     * @return string html representation of the provided errors
     */
    private function getFieldErrorsAsString($name, $errors) {
        $string = ucfirst($name) . ': ';

        if (count($errors) == 1) {
            $error = array_pop($errors);
            $string .= $error;
        } else {
            $string .= '<ul>';
            foreach ($errors as $error) {
                $string .= '<li>' . $error . '</li>';
            }
            $string .= '</ul>';
        }

        return $string;
    }

}