<?php

namespace zibo\library\i18n\translation;

use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\translation\io\TranslationIO;

use zibo\ZiboException;

/**
 * Translator of keys into localized translations
 */
class Translator {

    /**
     * The locale code for which this translator translates
     * @var string
     */
    protected $locale;

    /**
     * The script to get the plural translation code
     * @var string
     */
    protected $pluralScript;

    /**
     * The translation input/output implementation
     * @var zibo\library\translation\io\TranslationIO
     */
    protected $io;

    /**
     * Constructs a new translator
     * @param zibo\library\i18n\locale\Locale $locale
     * @param zibo\library\i18n\translation\io\TranslationIO $io
     */
    public function __construct(Locale $locale, TranslationIO $io) {
        $this->locale = $locale->getCode();
        $this->pluralScript = $locale->getPluralScript();

        $this->io = $io;
    }

    /**
     * Translates a key into a localized translation
     * @param string $key translation key
     * @param array $vars variables to be replaced in the translation
     * @param string $default default translation
     * @return string the key translated into a localized translation
     */
    public function translate($key, $vars = null, $default = null) {
        if ($default == null) {
            $default = '[' . $key . ']';
        }

        $translation = $this->io->getTranslation($this->locale, $key);

        if (!$translation) {
            $translation = $default;
        }

        if ($translation === null || $vars === null) {
            return $translation;
        }

        if (!is_array($vars)) {
            $vars = array('1' => $vars);
        }

        foreach ($vars as $key => $value) {
            $translation = str_replace('%' . $key . '%', $value, $translation);
        }

        return $translation;
    }

    /**
     * Translates a key into a localized translation which describes multiple items
     * @param int $n the number of items which the translation is describing
     * @param string $key translation key
     * @param array $vars variables to be replaced in the translation
     * @param string $default default translation
     * @return string the key translated into a localized translation
     * @uses zibo\library\i18n\locale\Locale::getPluralCode()
     */
    public function translatePlural($n, $key, $vars = null, $default = null) {
        if (is_null($vars)) {
            $vars = array();
        } elseif (!is_array($vars)) {
            $vars = array(1 => $vars);
        }
        $vars['n'] = $n;

        if ($this->pluralScript) {
            $keySuffix = (int) eval('return ' . $this->pluralScript . ';');
            $key .= '.' . $keySuffix;
        }

        return $this->translate($key, $vars, $default);
    }

    /*
    * Gets all the translations
    * @return array An associative array with translation key - value pairs
    */
    public function getAllTranslations() {
        return $this->io->getAllTranslations($this->locale);
    }

    /**
     * Sets a translation in this translator
     * @param string $key Key of the translation
     * @param string $translation Translation string
     * @return null
     */
    public function setTranslation($key, $translation) {
        $this->io->setTranslation($this->locale, $key, $translation);
    }

}