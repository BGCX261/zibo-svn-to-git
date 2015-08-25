<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\ZiboException;

/**
 * Validator to check if a value is greater than a minimum, less than a maximum or
 * greater than a minimum and less than a maximum
 */
class MinMaxValidator extends NumericValidator {

    /**
     * Code for the error when the value is less then the minimum
     * @var string
     */
    const CODE_MINIMUM = 'error.validation.minimum';

    /**
     * Code for the error when the value is greater then the maximum
     * @var string
     */
    const CODE_MAXIMUM = 'error.validation.maximum';

    /**
     * Code for the error when the value is less then the minimum or greater then the maximum
     * @var string
     */
    const CODE_MINMAX = 'error.validation.minmax';

    /**
     * Message for the error when the value is less then the minimum
     * @var string
     */
    const MESSAGE_MINIMUM = '%value% has to be at least %minimum%';

    /**
     * Message for the error when the value is greater then the maximum
     * @var string
     */
    const MESSAGE_MAXIMUM = '%value% cannot be greater than %maximum%';

    /**
     * Message for the error when the value is less then the minimum or greater then the maximum
     * @var string
     */
    const MESSAGE_MINMAX = '%value% has to be between %minimum% and %maximum%';

    /**
     * Option key for the minimum value
     * @var string
     */
    const OPTION_MINIMUM = 'minimum';

    /**
     * Option key for the maximum value
     * @var string
     */
    const OPTION_MAXIMUM = 'maximum';

    /**
     * Option key for the minimum error value, a translation key is expected in this value
     * @var string
     */
    const OPTION_ERROR_MINIMUM = 'error.minimum';

    /**
     * Option key for the maximum error value, a translation key is expected in this value
     * @var string
     */
    const OPTION_ERROR_MAXIMUM = 'error.maximum';

    /**
     * Option key for the minimum maximum error value, a translation key is expected in this value
     * @var string
     */
    const OPTION_ERROR_MINMAX = 'error.minimum.maximum';

    /**
     * Minimum value
     * @var int
     */
    private $minimum;

    /**
     * Maximum value
     * @var int
     */
    private $maximum;

    /**
     * The error code which will be used for the minimum error
     * @var string
     */
    private $errorCodeMinimum;

    /**
     * The error code which will be used for the maximum error
     * @var string
     */
    private $errorCodeMaximum;

    /**
     * The error code which will be used for the minimum-maximum error
     * @var string
     */
    private $errorCodeMinmax;

    /**
     * Construct a new minimum maximum validator
     * @param array $options options for the validator
     * @return null
     * @throws zibo\ZiboException when no minimum option or maximum option is provided
     * @throws zibo\ZiboException when the minimum or maximum is not a numeric value
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        if (!isset($options[self::OPTION_MINIMUM]) && !isset($options[self::OPTION_MAXIMUM])) {
            throw new ZiboException('Minimum nor maximum provided');
        }

        if (isset($options[self::OPTION_MINIMUM])) {
            if (!parent::isValid($options[self::OPTION_MINIMUM])) {
                throw new ZiboException('Invalid minimum provided');
            }
            $this->minimum = $options[self::OPTION_MINIMUM];
        }

        if (isset($options[self::OPTION_MAXIMUM])) {
            if (!parent::isValid($options[self::OPTION_MAXIMUM])) {
                throw new ZiboException('Invalid maximum provided');
            }
            $this->maximum = $options[self::OPTION_MAXIMUM];
        }

        if (array_key_exists(self::OPTION_ERROR_MINIMUM, $options)) {
            $this->errorCodeMinimum = $options[self::OPTION_ERROR_MINIMUM];
        } else {
            $this->errorCodeMinimum = self::CODE_MINIMUM;
        }

        if (array_key_exists(self::OPTION_ERROR_MAXIMUM, $options)) {
            $this->errorCodeMaximum = $options[self::OPTION_ERROR_MAXIMUM];
        } else {
            $this->errorCodeMaximum = self::CODE_MAXIMUM;
        }

        if (array_key_exists(self::OPTION_ERROR_MINMAX, $options)) {
            $this->errorCodeMinmax = $options[self::OPTION_ERROR_MINMAX];
        } else {
            $this->errorCodeMinmax = self::CODE_MINMAX;
        }
    }

    /**
     * Checks whether a value is a numeric value with a minimum and/or maximum
     * @param mixed $value
     * @return boolean true if the value is a valid value, false otherwise
     */
    public function isValid($value) {
        $result = parent::isValid($value);
        if (!$result) {
            return false;
        }

        if (!$this->isRequired && empty($value)) {
            return true;
        }

        if ($this->minimum !== null && $this->maximum !== null) {
            if ($value < $this->minimum || $value > $this->maximum) {
                $this->addMinMaxError($value);
                return false;
            }
        } elseif ($this->minimum !== null) {
            if ($value < $this->minimum) {
                $this->addMinimumError($value);
                return false;
            }

        } else if ($this->maximum !== null) {
            if ($value > $this->maximum) {
                $this->addMaximumError($value);
                return false;
            }
        }

        return true;
    }

    /**
     * Add a validation error for a value less then the minimum or greater then the maximum
     * @param array $value
     * @return null
     */
    private function addMinMaxError($value) {
        $parameters = array(
            'value' => $value,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
        );
        $this->addValidationError($this->errorCodeMinmax, self::MESSAGE_MINMAX, $parameters);
    }

    /**
     * Add a validation error for a value greater then the maximum
     * @param array $value
     * @return null
     */
    private function addMaximumError($value) {
        $parameters = array(
            'value' => $value,
            'maximum' => $this->maximum,
        );
        $this->addValidationError($this->errorCodeMaximum, self::MESSAGE_MAXIMUM, $parameters);
    }

    /**
     * Add a validation error for a value less then the minimum
     * @param array $value
     * @return null
     */
    private function addMinimumError($value) {
        $parameters = array(
            'value' => $value,
            'minimum' => $this->minimum,
        );
        $this->addValidationError($this->errorCodeMinimum, self::MESSAGE_MINIMUM, $parameters);
    }

}