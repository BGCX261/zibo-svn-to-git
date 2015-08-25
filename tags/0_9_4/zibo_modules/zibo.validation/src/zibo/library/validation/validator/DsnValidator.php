<?php

namespace zibo\library\validation\validator;

/**
 * Validator for a DSN string
 */
class DsnValidator extends UrlValidator {

    /**
     * Code for the error when the value is not a valid DSN
     * @var string
     */
    const CODE = 'error.validation.dsn';

    /**
     * Message for the error when the value is not a valid DSN
     * @var string
     */
    const MESSAGE = '%value% is not a valid dsn';

    /**
     * Construct the validator
     * @param array $options options for the validator, not applicable on this implementation
     * @return null
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        $tokenChars = $this->regexAlphaDigit . '|[_-]|\\.';

        $regexDatabase = '(\\/(' . $tokenChars . ')+)*\\/(' . $tokenChars . ')+';
        $regexDsn = $this->regexScheme . ':\\/\\/(' . $this->regexLogin . ')?' . $regexDatabase;
        $this->regex = '/^' . $regexDsn . '$/';

        $this->code = self::CODE;
        $this->message = self::MESSAGE;
    }

}