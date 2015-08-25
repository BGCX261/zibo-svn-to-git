<?php

namespace zibo\library\i18n\translation;

use zibo\library\i18n\locale\Locale;

use zibo\library\ObjectFactory;

/**
 * Manager of the translators
 */
class Manager {

    /**
     * Configuration key of the translation input/output implementation
     * @var string
     */
    const CONFIG_IO = 'i18n.translation.io';

    /**
     * Class name of the default implementation of the translation input/output
     * @var string
     */
    const CLASS_IO = 'zibo\\library\\i18n\\translation\\io\\IniTranslationIO';

    /**
     * Class name of the translation input/output interface
     * @var string
     */
    const INTERFACE_IO = 'zibo\\library\\i18n\\translation\\io\\TranslationIO';

    /**
     * Array with the loaded translators
     * @var array
     */
    private $translators;

    /**
     * Constructs a new translation manager
     * @return null
     *
     * @uses zibo\library\ObjectFactory::createFromConfig()
     */
    public function __construct() {
        $objectFactory = new ObjectFactory();
        $this->io = $objectFactory->createFromConfig(self::CONFIG_IO, self::CLASS_IO, self::INTERFACE_IO);

        $this->translators = array();
    }

    /**
     * Gets the translator for the provided locale
     * @param zibo\library\i18n\locale\Locale $locale
     * @return zibo\library\i18n\translation\Translator
     */
    public function getTranslator(Locale $locale) {
        $localeCode = $locale->getCode();
        if (array_key_exists($localeCode, $this->translators)) {
            return $this->translators[$localeCode];
        }

        return $this->translators[$localeCode] = new Translator($locale, $this->io);
    }

}
