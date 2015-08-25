<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\library\i18n\locale\io\LocaleIO;

use zibo\library\i18n\I18n;

/**
 * Negotiator that determines which locale should be used based on the HTTP Accept-Language request header,
 * as described in {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4 RFC 2616 section 14.4}.
 *
 * eg. es,en-us;q=0.7,ar-lb;q=0.3
 */
class HttpNegotiator implements Negotiator {

    /**
     * Separator between the locales in the HTTP Accept-Language header
     * @var string
     */
    const SEPARATOR_LOCALES = ',';

    /**
     * Separator between a locale and it's parameters in the HTTP Accept-Language header
     * @var string
     */
    const SEPARATOR_PARAMETERS = ';';

    /**
     * Separator of a locale between the language and the territory
     * @var string
     */
    const SEPARATOR_TERRITORY = '-';

    /**
     * Determines which locale to use, based on the HTTP Accept-Language header.
     * @param zibo\library\i18n\locale\io\LocaleIO $io the locale input/output
     * @return null|zibo\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleIO $io) {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        $acceptLanguages = $this->getAcceptLanguages();

        foreach ($acceptLanguages as $acceptLanguage) {
            $locale = $this->getLocaleFromAcceptLanguage($io, $acceptLanguage);
            if ($locale) {
                return $locale;
            }
        }

        return null;
    }

    /**
     * Tries to get a locale from the provided accept language
     * @param zibo\library\i18n\locale\io\LocaleIO $io
     * @param string $acceptLanguage
     * @return null|zibo\library\i18n\locale\Locale the locale
     */
    private function getLocaleFromAcceptLanguage(LocaleIO $io, $acceptLanguage) {
        if (strpos($acceptLanguage, self::SEPARATOR_PARAMETERS) === false) {
            $locale = $acceptLanguage;
        } else {
            list($locale, $parameters) = explode(self::SEPARATOR_PARAMETERS, $acceptLanguage);
        }
        if (strpos($locale, self::SEPARATOR_TERRITORY) === false) {
            $language = $locale;
            $territory = '';
        } else {
            list($language, $territory) = explode(self::SEPARATOR_TERRITORY, $locale);
        }

        $language = strtolower($language);
        $territory = strtoupper($territory);

        if (!empty($territory)) {
            $localeCode = $language . '_' . $territory;
            $locale = $io->getLocale($localeCode);
            if ($locale) {
                return $locale;
            }
        }

        $locale = $io->getLocale($language);
        if ($locale) {
            return $locale;
        }

        return null;
    }

    /**
     * Get the accepted languages (with parameters) from the HTTP Accept-Language request header
     *
     * @return array the accepted languages
     */
    private function getAcceptLanguages() {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        $acceptLanguages = explode(self::SEPARATOR_LOCALES, $acceptLanguage);

        return $acceptLanguages;
    }

}