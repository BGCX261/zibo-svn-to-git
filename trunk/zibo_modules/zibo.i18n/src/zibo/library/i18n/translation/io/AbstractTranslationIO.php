<?php

namespace zibo\library\i18n\translation\io;

use zibo\ZiboException;

/**
 * Abstract implementation of the TranslationIO
 */
abstract class AbstractTranslationIO implements TranslationIO {

    /**
     * Array holding the read translations
     * @var array
     */
    protected $translations = array();

    /**
     * Gets a translation for the provided locale code
     * @param string $localeCode code of the locale
     * @param string $key key of the translation
     * @return string|null astring when found, null otherwise
     */
    public function getTranslation($localeCode, $key) {
        if (!isset($this->translations[$localeCode])) {
            $this->getTranslations($localeCode);
        }

        if (isset($this->translations[$localeCode][$key])) {
            return $this->translations[$localeCode][$key];
        }

        return null;
    }

    /**
     * Sets a translation for the provided locale
     * @param string $localeCode Code of the locale
     * @param string $key Key of the translation
     * @param string $translation
     * @return null
     * @throws zibo\ZiboException when this functionality is not supported
     */
    public function setTranslation($localeCode, $key, $translation) {
        throw new ZiboException('Setting a translation is not supported');
    }

}