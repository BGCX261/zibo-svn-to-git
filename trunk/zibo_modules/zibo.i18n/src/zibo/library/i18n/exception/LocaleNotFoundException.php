<?php

namespace zibo\library\i18n\exception;

use zibo\ZiboException;

/**
 * Exception for when a requested locale is not found
 */
class LocaleNotFoundException extends ZiboException {

    /**
     * Constructs a new locale not found exception
     * @param string $localeCode Code of the requested locale
     * @return null
     */
    public function __construct($localeCode) {
        parent::__construct('Locale ' . $localeCode . ' is not defined in this Zibo installation');
    }

}