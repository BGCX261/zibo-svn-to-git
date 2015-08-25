<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Validator to check if a value matches a regular expression
 */
class RegexValidator extends AbstractValidator {

    /**
     * Code for the error when the regular expression is not matched
     * @var string
     */
    const CODE = 'error.validation.regex';

    /**
     * Option key for the regular expression
     * @var string
     */
    const OPTION_REGEX = 'regex';

    /**
     * Option key to see if a value is required
     * @var string
     */
    const OPTION_REQUIRED = 'required';

    /**
     * Code of the error when the regular expression is not matched
     * @var string
     */
    protected $code;

    /**
     * Message of the error when the regular expression is not matched
     * @var string
     */
    protected $message;

    /**
     * Regular expression for this validator
     * @var string
     */
    protected $regex;

    /**
     * Flag to see if a value is required
     * @var boolean
     */
    protected $isRequired;

    /**
     * Construct a new regular expression validator
     * @param array $options options for this validator
     * @return null
     * @throws zibo\ZiboException when the regex option is empty or not a string
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        if (!isset($options[self::OPTION_REGEX])) {
            throw new ZiboException('No regular expression provided through the options. Use option ' . self::OPTION_REGEX);
        }
        if (!String::isString($options[self::OPTION_REGEX], String::NOT_EMPTY)) {
            throw new ZiboException('Provided regular expression is empty');
        }

        $this->isRequired = true;
        if (isset($options[self::OPTION_REQUIRED])) {
            $this->isRequired = $options[self::OPTION_REQUIRED];
        }

        $this->regex = $options[self::OPTION_REGEX];
        $this->code = self::CODE;
        if (isset($options['message'])) {
            $this->code = $options['message'];
        }
        $this->message = 'Field does not match ' . $this->regex;
    }

    /**
     * Checks whether a value matches the regular expression
     * @param mixed $value
     * @return boolean true if the value matches the regular expression or is empty and a value is not required, false otherwise
     */
    public function isValid($value) {
        $this->resetErrors();

        if (!$this->isRequired && empty($value)) {
            return true;
        }

        if (!preg_match($this->regex, $value)) {
            $parameters = array(
               'value' => $value,
               'regex' => $this->regex,
            );
            $error = new ValidationError($this->code, $this->message, $parameters);
            $this->addError($error);
            return false;
        }

        return true;
    }

}