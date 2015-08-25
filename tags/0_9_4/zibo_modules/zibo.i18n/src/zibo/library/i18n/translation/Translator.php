<?php

namespace zibo\library\i18n\translation;

use zibo\core\Zibo;

use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\translation\io\TranslationIO;

use zibo\ZiboException;

/**
 * Translator of keys into localized translations
 */
class Translator {

    /**
     * The locale for which this translator translates
     * @var zibo\library\i18n\locale\Locale
     */
    protected $locale;

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
        $this->locale = $locale;
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

        $translation = $this->io->getTranslation($this->locale->getCode(), $key);

        if (!$translation) {
            $translation = $default;
        }

        if ($vars == null) {
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

        $plural = (int) eval('return ' . $this->locale->getPluralCode() . ';');
        $key .= ".$plural";

        return $this->translate($key, $vars, $default);
    }

    /**
     * Sets a translation in this translator
     * @param string $key Key of the translation
     * @param string $translation Translation string
     * @return null
     */
    public function setTranslation($key, $translation) {
        $this->io->setTranslation($this->locale->getCode(), $key, $translation);
    }

}