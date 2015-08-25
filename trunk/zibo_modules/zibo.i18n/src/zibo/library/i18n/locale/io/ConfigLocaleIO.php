<?php

namespace zibo\library\i18n\locale\io;

use zibo\core\Zibo;

use zibo\library\i18n\locale\Locale;

/**
 * Implementation of LocaleIO that reads localization data from the Zibo
 * configuration
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
     * Configuration key to read the locale data from
     * @var string
     */
    private $configKey;

    /**
     * Constructs a new config LocaleIO
     * @param string $configKey which config key to read locale data from,
     * defaults to ConfigLocaleIO::CONFIG_LOCALES
     * @return null
     */
    public function __construct(Zibo $zibo, $configKey = self::CONFIG_LOCALES) {
        $this->zibo = $zibo;
        $this->configKey = $configKey;
    }

    /**
     * Gets all available locales from the Zibo configuration
     * @return array all Locale objects
     */
    public function getLocales() {
        $locales = array();

        $localesConfig = $this->zibo->getConfigValue($this->configKey, array());

        foreach ($localesConfig as $code => $options) {
            $locales[$code] = $this->createLocaleObject($code, $options);
        }

        return $locales;
    }

    /**
     * Creates an instance of the Locale class with the given code and options,
     * and puts it in the retrieved locale list
     * @param string $code
     * @param array $options
     * @return zibo\library\i18n\locale\Locale
     */
    private function createLocaleObject($code, array $options = array()) {
        $name = $code;
        $pluralScript = null;

        if (isset($options[self::OPTION_NAME])) {
            $name = $options[self::OPTION_NAME];
        }
        if (isset($options[self::OPTION_PLURAL])) {
            $pluralScript = $options[self::OPTION_PLURAL];
        }

        $locale = new Locale($code, $name, $pluralScript);

        $this->locales[$code] = $locale;

        if (isset($options[self::OPTION_FORMAT])) {
            $this->setFormats($options[self::OPTION_FORMAT], $locale);
        }

        return $locale;
    }

    /**
     * Sets the formats of the locale
     * @param array $formats
     * @param zibo\library\i18n\locale\Locale $locale
     * @return null
     */
    private function setFormats($formats, Locale $locale) {
        if (isset($formats[self::OPTION_DATE])) {
            foreach ($formats[self::OPTION_DATE] as $identifier => $format) {
                $locale->setDateFormat($identifier, $format);
            }
        }
    }

}