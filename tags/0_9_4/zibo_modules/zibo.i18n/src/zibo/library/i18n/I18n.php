<?php

namespace zibo\library\i18n;

use zibo\library\i18n\exception;

use zibo\core\Zibo;

use zibo\library\i18n\exception\LocaleNotFoundException;

use zibo\library\i18n\locale\Locale;

use zibo\library\i18n\locale\Manager as LocaleManager;
use zibo\library\i18n\translation\Manager as TranslationManager;

use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

use \InvalidArgumentException;

/**
 * Facade to the localization and translation subsystems
 */
class I18n {

    /**
     * Instance of this facade
     * @var zibo\library\i18n\I18n
     */
    private static $instance;

    /**
     * The manager of the locales
     * @var zibo\library\i18n\locale\Manager
     */
    protected $localeManager;

    /**
     * The manager of the translators
     * @var zibo\library\i18n\translation\Manager
     */
    protected $translationManager;

    /**
     * Constructs a new internationalization facade
     * @return null
     */
    private function __construct() {
        $this->localeManager = new LocaleManager();
        $this->translationManager = new TranslationManager();
    }

    /**
     * Get the instance of this facade
     * @return zibo\library\i18n\I18n the unique instance of I18n
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Checks if a locale is available
     *
     * @param string $code the locale code
     * @return boolean true if the locale is available, or false if not
     */
    public function hasLocale($code) {
        $locale = $this->localeManager->getLocale($code);
        return ($locale instanceof Locale);
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
        $locale = $this->localeManager->getLocale($code);
        if (!$locale) {
            throw new LocaleNotFoundException($code);
        }

        return $locale;
    }

    /**
     * Sets the current locale
     * @param zibo\\i18n\locale\Locale $locale
     * @return null
     */
    public function setCurrentLocale(Locale $locale) {
        $this->localeManager->setCurrentLocale($locale);
    }

    /**
     * Gets all the available locales
     * @return array
     *
     * @uses zibo\library\i18n\locale\Manager::getAllLocales()
     */
    public function getAllLocales() {
        return $this->localeManager->getAllLocales();
    }

    /**
     * Gets the translator for a locale
     * @param null|string|zibo\library\i18n\locale\Locale $locale the locale or locale code, if not specified the current locale is assumed
     * @return zibo\library\i18n\translation\Translator
     *
     * @throws zibo\ZiboException when $locale is not specified and no locales could be found
     */
    public function getTranslator($locale = null) {
        if ($locale === null || is_string($locale)) {
            $locale = $this->getLocale($locale);
        } else if (!$locale instanceof Locale) {
            throw new InvalidArgumentException('Unexpected $locale argument of type ' . gettype($locale) . ', expected a string or an instance of zibo\\library\\i18n\\locale\\Locale');
        }

        return $this->translationManager->getTranslator($locale);
    }

    /**
     * @todo do we need this function? where is it used?
     */
    public function getLocaleList() {
        $locales = array();
        foreach ($this->getAllLocales() as $locale) {
            $locales[$locale->getCode()] = $locale->getNativeName();
        }
        return $locales;
    }

    /**
     * @todo do we need this function? where is it used?
     */
    public function getLocaleCodeList() {
        $locales = array();
        foreach ($this->getAllLocales() as $locale) {
            $code = $locale->getCode();
            $locales[$code] = $code;
        }
        return $locales;
    }

}