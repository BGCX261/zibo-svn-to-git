<?php

namespace zibo\library\html\table\decorator;

use zibo\library\i18n\I18n;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Decorator for a date value
 */
class DateDecorator extends ValueDecorator {

    /**
     * Default value to decorate a cell with if the date value could not be formatted
     * @var string
     */
    const DEFAULT_VALUE = '---';

    /**
     * The name of the date format
     * @var string
     */
    private $format;

    /**
     * The current locale
     * @var zibo\library\i18n\locale\Locale
     */
    private $locale;

    /**
     * Constructs a new decorator
     * @param string $fieldName field name for objects or arrays passed to this decorator (optional)
     * @param string $format Name of the date format
     * @param string $defaultValue Value to decorate the cell with if the date value could not be formatted
     * @return null
     * @throws zibo\ZiboException when an invalid field name or format is provided
     */
    public function __construct($fieldName = null, $format = null, $defaultValue = null) {
        parent::__construct($fieldName);

        $this->setFormat($format);

        $this->locale = I18n::getInstance()->getLocale();
    }

    /**
     * Sets the date format
     * @param string $format
     * @return null
     * @throws zibo\ZiboException when an invalid format is provided
     */
    private function setFormat($format = null) {
        if ($format !== null && String::isEmpty($format)) {
            throw new ZiboException('Provided format is empty');
        }

        $this->format = $format;
    }

    /**
     * Performs the actual decorating on the provided value.
     * @param mixed $value The value to decorate
     * @return mixed The decorated value
     */
    protected function decorateValue($value) {
        if (!$value) {
            return self::DEFAULT_VALUE;
        }

        try {
            $value = $this->locale->formatDate($value, $this->format);
        } catch (Exception $e) {
            $value = self::DEFAULT_VALUE;
        }

        return $value;
    }

}