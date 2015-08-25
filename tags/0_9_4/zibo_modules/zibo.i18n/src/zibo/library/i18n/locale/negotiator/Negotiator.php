<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\library\i18n\locale\io\LocaleIO;

/**
 * Locale negotiator
 */
interface Negotiator {

    /**
     * Determines which locale to use
     *
     * @param zibo\library\i18n\locale\io\LocaleIO $io the locale input/output
     * @return null|zibo\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleIO $io);

}