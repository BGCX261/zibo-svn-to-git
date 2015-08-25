<?php

namespace zibo\library\i18n\translation;

use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\translation\io\TranslationIO;

/**
 * Manager of the translators
 */
class TranslationManager {

    /**
     * Array with the loaded translators
     * @var array
     */
    private $translators;

    /**
     * Constructs a new translation manager
     * @param zibo\library\i18n\translation\io\TranslationIO $io
     * @return null
     */
    public function __construct(TranslationIO $io) {
        $this->io = $io;
        $this->translators = array();
    }

    /**
     * Gets the translator for the provided locale
     * @param zibo\library\i18n\locale\Locale $locale
     * @return zibo\library\i18n\translation\Translator
     */
    public function getTranslator(Locale $locale) {
        $localeCode = $locale->getCode();

        if (isset($this->translators[$localeCode])) {
            return $this->translators[$localeCode];
        }

        return $this->translators[$localeCode] = new Translator($locale, $this->io);
    }

}
