<?php

namespace zibo\library\i18n\translation\io;

use zibo\core\Zibo;

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
        if (!array_key_exists($localeCode, $this->translations)) {
            $this->getAllTranslations($localeCode);
        }

        if (array_key_exists($key, $this->translations[$localeCode])) {
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