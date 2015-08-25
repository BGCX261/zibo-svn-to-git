<?php

/**
 * @package zibo-library-i18n-exception
 */
namespace zibo\library\i18n\exception;

use zibo\ZiboException;

/**
 * Exception for when a requested locale is not found
 */
class LocaleNotFoundException extends ZiboException {

    /**
     *
     * @param string $localeCode
     */
    public function __construct($localeCode) {
        parent::__construct('Locale ' . $localeCode . ' is not defined in this Zibo installation');
    }

}