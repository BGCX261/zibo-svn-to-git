<?php

namespace zibo\library\i18n\translation\io;

/**
 * Interface for the translation input/output
 */
interface TranslationIO {

    /*
     * Gets a translation for the provided locale code
     * @param string $localeCode Code of the locale
     * @param string $key Key of the translation
     * @return string|null A string when found, null otherwise
     */
    public function getTranslation($localeCode, $key);

    /*
     * Gets all the translations for the provided locale
     * @param string $localeCode Code of the locale
     * @return array An associative array with translation key - value pairs
     */
    public function getTranslations($localeCode);

    /**
     * Sets a translation for the provided locale
     * @param string $localeCode Code of the locale
     * @param string $key Key of the translation
     * @param string $translation
     * @return null
     */
    public function setTranslation($localeCode, $key, $translation);

}