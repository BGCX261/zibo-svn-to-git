<?php

namespace zibo\library\i18n\locale\io;

use zibo\core\Zibo;

use zibo\library\i18n\locale\Locale;

/**
 * Implementation of LocaleIO that reads localization data from the Zibo configuration
 */
class ConfigLocaleIO implements LocaleIO {

    /**
     * Configuration key for the locales
     * @var string
     */
    const CONFIG_LOCALES = 'l10n';

    /**
     * Configuration name of the name option
     * @var string
     */
    const OPTION_NAME = 'name';

    /**
     * Configuration name of the native name option
     * @var string
     */
    const OPTION_NATIVE_NAME = 'native';

    /**
     * Configuration name of the plural code option
     * @var string
     */
    const OPTION_PLURAL = 'plural';

    /**
     * Configuration name of the formats option
     * @var string
     */
    const OPTION_FORMAT = 'format';

    /**
     * Configuration name of the date format
     * @var string
     */
    const OPTION_DATE = 'date';

    /*
     * @var string which config key to read locale data from
     */
    private $configKey;

    /**
     *
     * @var array array of Locale objects
     */
    private $locales;

    /**
     * Constructs a new config LocaleIO
     * @param string $configKey which config key to read locale data from, defaults to ConfigLocaleIO::CONFIG_LOCALES
     * @return null
     */
    public function __construct($configKey = self::CONFIG_LOCALES) {
        $this->configKey = $configKey;
        $this->locales = array();
    }

    /**
     * Gets all available locales
     * @return array all Locale objects
     */
    public function getAllLocales() {
        $allLocalesConfig = Zibo::getInstance()->getConfigValue($this->configKey, array());

        $allLocaleCodes = array_keys($allLocalesConfig);
        $alreadyRetrievedLocaleCodes = array_keys($this->locales);
        $notYetRetrievedLocaleCodes = array_diff($allLocaleCodes, $alreadyRetrievedLocaleCodes);

        foreach ($notYetRetrievedLocaleCodes as $code) {
            $options = $allLocalesConfig[$code];
            $this->createLocaleObject($code, $options);
        }

        return $this->locales;
    }

    /**
     * Gets the locale for the given code
     * @param string $code the locale code
     * @return zibo\library\i18n\locale\Locale|null
     */
    public function getLocale($code) {
        if (array_key_exists($code, $this->locales)) {
            return $this->locales[$code];
        } else {
            $options = Zibo::getInstance()->getConfigValue($this->configKey . '.' . $code, null);
            if (is_array($options)) {
                $locale = $this->createLocaleObject($code, $options);
                return $locale;
            }
        }
    }

    /**
     * Creates an instance of the Locale class with the given code and options, and puts it in the
     * retrieved locale list
     *
     * @param string $code
     * @param array $options
     * @return zibo\library\i18n\locale\Locale
     */
    private function createLocaleObject($code, array $options = array()) {
        $name = $code;
        $native = $code;
        $plural = null;

        if (isset($options[self::OPTION_NAME])) {
            $name = $options[self::OPTION_NAME];
        }
        if (isset($options[self::OPTION_NATIVE_NAME])) {
            $native = $options[self::OPTION_NATIVE_NAME];
        }
        if (isset($options[self::OPTION_PLURAL])) {
            $plural = $options[self::OPTION_PLURAL];
        }

        $locale = new Locale($code, $name, $native, $plural);
        $this->locales[$code] = $locale;

        if (isset($options[self::OPTION_FORMAT])) {
            $this->setFormats($options[self::OPTION_FORMAT], $locale);
        }

        return $locale;
    }

    /**
     * Sets the formats of the locale
     *
     * @param array $formats
     * @param zibo\library\i18n\locale\Locale $locale
     */
    private function setFormats($formats, Locale $locale) {
        if (isset($formats[self::OPTION_DATE])) {
            foreach ($formats[self::OPTION_DATE] as $identifier => $format) {
                $locale->setDateFormat($identifier, $format);
            }
        }
    }

}