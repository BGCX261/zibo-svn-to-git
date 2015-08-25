<?php

namespace zibo\library\i18n\locale;

use zibo\library\String;

use zibo\ZiboException;

use \DateTime;

/**
 * Container of locale information and holder of localization actions
 */
class Locale {

    /**
     * The default date format
     * @var string
     */
    const DEFAULT_DATE_FORMAT = 'm/d/Y H:i:s';

    /**
     * The identifier of the default date format
     * @var string
     */
    const DEFAULT_DATE_IDENTIFIER = 'default';

    /**
     * The code of the locale
     * @var string
     */
    private $code;

    /**
     * The native name of the locale
     * @var string
     */
    private $name;

    /**
     * PHP script which return a translation key suffix
     * @var string
     */
    private $pluralScript;

    /**
     * Array of the defined date formats
     * @var array
     */
    private $dateFormats;

    /**
     * Constructs a new locale
     * @param string $code The code of the locale
     * @param string $name The native name of the locale
     * @param string $pluralScript PHP script which returns a translation key
     * suffix
     * @return null
     */
    public function __construct($code, $name, $pluralScript = null) {
        $this->setCode($code);
        $this->setName($name);
        $this->setPluralScript($pluralScript);
    }

    /**
     * Sets the code of this locale
     * @param string $code The code of this locale
     * @throws zibo\core\ZiboException when the provided code is empty or invalid
     */
    private function setCode($code) {
        if (!String::isString($code, String::NOT_EMPTY)) {
            throw new ZiboException('Provided locale code is empty');
        }

        $this->code = $code;
    }

    /**
     * Gets the code of this locale
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Sets the native name of this locale
     * @param string $name The native name of this locale
     * @return null
     * @throws zibo\core\ZiboException when the provided name is empty or invalid
     */
    private function setName($name) {
        if (!String::isString($name, String::NOT_EMPTY)) {
            throw new ZiboException('Provided locale native name is empty');
        }

        $this->name = $name;
    }

    /**
     * Gets the native name of this locale
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the plural script for the translator
     * @param string $plural PHP script which returns a translation key suffix.
     * @return null
     */
    private function setPluralScript($plural) {
        $this->plural = $plural;
    }

    /**
     * Sets the plural script for the translator
     * @return string PHP script which returns a translation key suffix.
     */
    public function getPluralScript() {
        return $this->plural;
    }

    /**
     * Sets a date format
     * @param string $formatIdentifier id of the date format
     * @param string $format The date format
     * @throws zibo\core\ZiboException when the id or the format are empty or
     * invalid
     */
    public function setDateFormat($formatIdentifier, $format) {
        if (!String::isString($formatIdentifier, String::NOT_EMPTY)) {
            throw new ZiboException('Provided identifier is empty');
        }
        if (!String::isString($format, String::NOT_EMPTY)) {
            throw new ZiboException('Provided format is empty');
        }

        $this->dateFormats[$formatIdentifier] = $format;
    }

    /**
     * Checks if the provided date format is defined in this locale
     * @param string $formatIdentifier Id of the date format
     * @return boolean
     */
    public function hasDateFormat($formatIdentifier) {
        return isset($this->dateFormats[$formatIdentifier]);
    }

    /**
     * Gets the format for the provided id
     * @param string $formatIdentifier Id of the date format
     * @return string
     */
    public function getDateFormat($formatIdentifier = null) {
        if (!$formatIdentifier) {
            $formatIdentifier = self::DEFAULT_DATE_IDENTIFIER;
        }

        if (isset($this->dateFormats[$formatIdentifier])) {
            return $this->dateFormats[$formatIdentifier];
        }

        if ($formatIdentifier === self::DEFAULT_DATE_IDENTIFIER) {
            return self::DEFAULT_DATE_FORMAT;
        }

        return $formatIdentifier;
    }

    /**
     * Gets the defined date formats
     * @return array Array with the id of the format as key and the format as
     * value
     */
    public function getDateFormats() {
        return $this->dateFormats;
    }

    /**
     * Formats a timestamp into a formatted string
     * @param integer $timestamp The timestamp to format
     * @param string $formatIdentifier Id of the format
     * @return string The formatted date
     */
    public function formatDate($timestamp, $formatIdentifier = null) {
        $formatPattern = $this->getDateFormat($formatIdentifier);

        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);

        return $dateTime->format($formatPattern);
    }

    /**
     * Parses a formatted date into it's timestamp
     * @param string $date A formatted date
     * @param string $formatIdentifier Id of the format
     * @return integer Timestamp of the provided date
     * @throws zibo\core\ZiboException when the date could not be parsed
     */
    public function parseDate($date, $formatIdentifier = null) {
        $formatPattern = $this->getDateFormat($formatIdentifier);

        $dateTime = DateTime::createFromFormat($formatPattern, $date);
        if ($dateTime && $timestamp = $dateTime->getTimestamp()) {
            return $timestamp;
        }

        throw new ZiboException('Could not parse ' . $date . ' with format ' . $formatIdentifier . ' (' . $formatPattern . ')');
    }

}