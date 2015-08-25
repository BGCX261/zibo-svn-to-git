<?php

namespace zibo\library\i18n\locale;

use zibo\library\String;

use zibo\ZiboException;

use \DateTime;

/**
 * Container of locale information and holder of localization actions
 */
class Locale {

    const DEFAULT_DATE_FORMAT = 'm/d/Y';
    const DEFAULT_DATE_IDENTIFIER = 'default';

    private $code;
    private $name;
    private $native;
    private $settings;
    private $dateFormats;

    /**
     *
     * @param string $code
     * @param string $name
     * @param string $native
     * @param string $plural
     */
    public function __construct($code, $name, $native, $plural = null) {
        $this->setCode($code);
        $this->setName($name);
        $this->setNativeName($native);
        $this->setPluralCode($plural);
    }

    private function setCode($code) {
        if (String::isEmpty($code)) {
            throw new ZiboException('Provided locale code is empty');
        }
        $this->code = $code;
    }

    public function getCode() {
        return $this->code;
    }

    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided locale name is empty');
        }
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    private function setNativeName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided locale native name is empty');
        }
        $this->native = $name;
    }

    public function getNativeName() {
        return $this->native;
    }

    private function setPluralCode($plural) {
        $this->plural = $plural;
    }

    public function getPluralCode() {
        return $this->plural;
    }

    public function formatDate($timestamp, $formatIdentifier = null) {
        $formatPattern = $this->getDateFormat($formatIdentifier);
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime->format($formatPattern);
    }

    public function parseDate($date, $formatIdentifier = null) {
        $formatPattern = $this->getDateFormat($formatIdentifier);
        $dateTime = DateTime::createFromFormat($formatPattern, $date);
        if ($dateTime && $timestamp = $dateTime->getTimestamp()) {
            return $timestamp;
        }
        throw new ZiboException('Could not parse ' . $date . ' with format ' . $formatIdentifier . ' (' . $formatPattern . ')');
    }

    public function getDateFormat($formatIdentifier = null) {
        if (!$formatIdentifier) {
            $formatIdentifier = self::DEFAULT_DATE_IDENTIFIER;
        }

        if (isset($this->dateFormats[$formatIdentifier])) {
            return $this->dateFormats[$formatIdentifier];
        }

        if ($formatIdentifier == self::DEFAULT_DATE_IDENTIFIER) {
            return self::DEFAULT_DATE_FORMAT;
        }

        return $formatIdentifier;
    }

    public function getDateFormats() {
        return $this->dateFormats;
    }

    public function setDateFormat($formatIdentifier, $format) {
        if (String::isEmpty($formatIdentifier)) {
            throw new ZiboException('Provided identifier is empty');
        }
        if (String::isEmpty($format)) {
            throw new ZiboException('Provided format is empty');
        }
        $this->dateFormats[$formatIdentifier] = $format;
    }

}