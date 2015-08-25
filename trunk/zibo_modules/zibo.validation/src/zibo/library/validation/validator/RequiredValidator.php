<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;
use zibo\library\String;

/**
 * Validator to check if a value is empty
 */
class RequiredValidator extends AbstractValidator {

    /**
     * Code of the error message when the value is empty
     * @var string
     */
    const CODE = 'error.validation.required';

    /**
     * Message of the error message when the value is empty
     * @var string
     */
    const MESSAGE = 'Field is required';

    /**
     * Option to change the error message when the value is empty, enter a translation key in this option
     * @var string
     */
    const OPTION_ERROR_REQUIRED = 'error.required';

    /**
     * The error code which will be used
     * @var string
     */
    private $errorCode;

    /**
     * Construct a new minimum maximum validator
     * @param array $options options for the validator
     * @return null
     * @throws zibo\ZiboException when no minimum option or maximum option is provided
     * @throws zibo\ZiboException when the minimum or maximum is not a numeric value
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        if (isset($options[self::OPTION_ERROR_REQUIRED])) {
            $this->errorCode = $options[self::OPTION_ERROR_REQUIRED];
        } else {
            $this->errorCode = self::CODE;
        }
    }

    /**
     * Checks whether a value is empty
     * @param mixed $value
     * @return boolean true if the value is empty, false otherwise
     */
    public function isValid($value) {
        $this->resetErrors();

        if (is_object($value)) {
            return true;
        }

        if (is_array($value)) {
            if (empty($value)) {
                $error = new ValidationError($this->errorCode, self::MESSAGE, array('value' => $value));
                $this->addError($error);
                return false;
            }
            return true;
        }

        if (!is_bool($value) && !String::isString($value, String::NOT_EMPTY)) {
            $error = new ValidationError($this->errorCode, self::MESSAGE, array('value' => $value));
            $this->addError($error);
            return false;
        }

        return true;
    }

}