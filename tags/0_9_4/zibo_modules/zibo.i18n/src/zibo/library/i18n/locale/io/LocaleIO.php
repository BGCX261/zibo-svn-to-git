<?php

namespace zibo\library\i18n\locale\io;

/**
 * Interface to retrieve locales
 */
interface LocaleIO {

    /**
     * Gets all available locales
     * @return array all Locale objects
     */
    public function getAllLocales();

    /**
     * Gets the locale for the given code
     * @param string $code the locale code
     * @return zibo\library\i18n\locale\Locale|null
     */
    public function getLocale($code);

}