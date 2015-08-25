<?php

namespace zibo\library\validation\validator;

/**
 * Validator to check whether a value is a valid website
 */
class WebsiteValidator extends UrlValidator {

    /**
     * Error code when the value is not a valid website
     * @var string
     */
    const CODE = 'error.validation.website';

    /**
     * Error message when the value is not a valid website
     * @var string
     */
    const MESSAGE = '\'%value%\' is not a valid website';

    /**
     * Construct a new website validator instance
     * @param array $options options for this instance
     * @return null
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        $this->regex = '/^' . $this->regexHttp . '$/';

        $this->code = self::CODE;
        $this->message = self::MESSAGE;
    }

}