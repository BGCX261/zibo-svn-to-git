<?php

namespace zibo\library\i18n;

use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\locale\LocaleManager;
use zibo\library\i18n\translation\TranslationManager;

/**
 * Facade to the localization and translation subsystems
 */
class I18n {

    /**
     * The manager of the locales
     * @var zibo\library\i18n\locale\LocaleManager
     */
    protected $localeManager;

    /**
     * The manager of the translators
     * @var zibo\library\i18n\translation\TranslationManager
     */
    protected $translationManager;

    /**
     * Constructs a new internationalization facade
     * @param zibo\library\i18n\locale\LocaleManager $localeManager Manager
     * of the locales
     * @param zibo\library\i18n\translation\TranslationManager $translation
     * Manager of the translations
     * @return null
     */
    public function __construct(LocaleManager $localeManager, TranslationManager $translationManager) {
        $this->localeManager = $localeManager;
        $this->translationManager = $translationManager;
    }

    /**
     * Sets the current locale
     * @param string $code The code of the locale
     * @return null
     */
    public function setCurrentLocale($code) {
        $this->localeManager->setCurrentLocale($code);
    }

    /**
     * Checks if the provided locale is available
     * @param string $code The code of the locale
     * @return boolean
     */
    public function hasLocale($code) {
        return $this->localeManager->hasLocale($code);
    }

    /**
     * Gets the locale.
     *
     * @param string $code the locale code, if not specified then the current locale is assumed
     * @return zibo\library\i18n\locale\Locale
     *
     * @throws zibo\library\i18n\exception\LocaleNotFoundException if the locale with the specified code could not be found
     * @throws zibo\ZiboException when $code is not specified and no locales could be found
     */
    public function getLocale($code = null) {
        return $this->localeManager->getLocale($code);
    }

    /**
     * Gets all the available locales
     * @return array
     */
    public function getLocales() {
        return $this->localeManager->getLocales();
    }

    /**
     * Gets a list of the available locales
     * @return array Array with the locale code as key and the native name as value
     */
    public function getLocaleList() {
        $locales = array();

        foreach ($this->getLocales() as $locale) {
            $locales[$locale->getCode()] = $locale->getName();
        }

        return $locales;
    }

    /**
     * Gets a list of the available locales
     * @return array Array with the locale code as key and as value
     */
    public function getLocaleCodeList() {
        $locales = array();

        foreach ($this->getLocales() as $locale) {
            $code = $locale->getCode();
            $locales[$code] = $code;
        }

        return $locales;
    }

    /**
     * Gets the translator for a locale
     * @param null|string|zibo\library\i18n\locale\Locale $locale locale code,
     * a Locale instance or if not specified the current locale is assumed
     * @return zibo\library\i18n\translation\Translator
     *
     * @throws zibo\ZiboException when $locale is not specified and no locales
     * could be found
     */
    public function getTranslator($locale = null) {
        if ($locale === null || !$locale instanceof Locale) {
            $locale = $this->getLocale($locale);
        }

        return $this->translationManager->getTranslator($locale);
    }

}