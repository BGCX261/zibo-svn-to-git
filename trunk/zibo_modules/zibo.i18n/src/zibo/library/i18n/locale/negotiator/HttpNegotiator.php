<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\core\Zibo;

use zibo\library\i18n\locale\LocaleManager;

/**
 * Negotiator that determines which locale should be used based on the HTTP Accept-Language
 * in the Zibo request, as described in
 * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4 RFC 2616 section 14.4}.
 *
 * eg. es,en-us;q=0.7,ar-lb;q=0.3
 */
class HttpNegotiator implements Negotiator {

    /**
     * Separator of a locale between the language and the territory
     * @var string
     */
    const SEPARATOR_TERRITORY = '-';

    /**
     * Instance of Zibo
     * @var zibo\core\Zibo
     */
    private $zibo;

    /**
     * Constructs a new HTTP negotiator
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return null
     */
    public function __construct(Zibo $zibo) {
        $this->zibo = $zibo;
    }

    /**
     * Determines which locale to use, based on the HTTP Accept-Language header.
     * @param zibo\library\i18n\locale\LocaleManager $manager The locale manager
     * @return null|zibo\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleManager $manager) {
        $request = $this->zibo->getRequest();
        if (!$request) {
            return null;
        }

        $fallbackLanguages = array();

        $acceptedLanguages = $request->getAcceptLanguage();
        foreach ($acceptedLanguages as $acceptedLanguage => $null) {
            if (strpos($acceptedLanguage, self::SEPARATOR_TERRITORY) === false) {
                $locale = strtolower($acceptedLanguage);
            } else {
                list($language, $territory) = explode(self::SEPARATOR_TERRITORY, $acceptedLanguage);
                $language = strtolower($language);
                $locale = $language . '_' . strtoupper($territory);

                $fallbackLanguages[$language] = true;
            }

            if ($manager->hasLocale($locale)) {
                return $manager->getLocale($locale);
            }
        }

        foreach ($fallbackLanguages as $locale => $null) {
            if ($manager->hasLocale($locale)) {
                return $manager->getLocale($locale);
            }
        }

        return null;
    }

}