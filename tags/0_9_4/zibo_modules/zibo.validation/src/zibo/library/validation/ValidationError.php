<?php

namespace zibo\library\validation;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Data container of a validation error
 */
class ValidationError {

    /**
     * Code of the error
     * @var string
     */
    private $code;

    /**
     * Message of the error
     * @var string
     */
    private $message;

    /**
     * Parameters for the error message
     * @var array
     */
    private $parameters;

    /**
     * Construct a new validation error
     * @param string $code code of the error
     * @param string $message message of the error
     * @param array $parameters parameters for the message of the error
     * @return null
     * @throws zibo\ZiboException when the provided code or message is empty or invalid
     */
    public function __construct($code, $message, array $parameters = array()) {
        $this->setCode($code);
        $this->setMessage($message);
        $this->parameters = $parameters;
    }

    /**
     * Get the message of this error as a string with the parameters parsed into
     * @return string
     */
    public function __toString() {
        if (!$this->parameters) {
            return $this->message;
        }

        $string = $this->message;

        foreach ($this->parameters as $key => $value) {
            if (!is_scalar($value)) {
                if (is_object($value)) {
                    $value = gettype($value);
                } else {
                    $value = 'Array';
                }
            }

            $string = str_replace('%' . $key . '%', $value, $string);
        }

        return $string;
    }

    /**
     * Set the code of this error
     * @param string $code
     * @return null
     * @throws zibo\ZiboException when the code is empty or invalid
     */
    private function setCode($code) {
        if (String::isEmpty($code)) {
            throw new ZiboException('Provided code is empty');
        }

        $this->code = $code;
    }

    /**
     * Get the code of this error
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Set the message of this error
     * @param string $message
     * @return null
     * @throws zibo\ZiboException when the message is empty or invalid
     */
    private function setMessage($message) {
        if (String::isEmpty($message)) {
            throw new ZiboException('Provided message is empty');
        }

        $this->message = $message;
    }

    /**
     * Get the message of this error
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Get the parameters for the message of this error
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

}