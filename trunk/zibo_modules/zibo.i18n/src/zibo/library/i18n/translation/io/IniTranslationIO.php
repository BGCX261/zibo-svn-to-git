<?php

namespace zibo\library\i18n\translation\io;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

/**
 * INI file implementation of the TranslationIO
 */
class IniTranslationIO extends AbstractTranslationIO {

    /**
     * The extension of the translation files
     * @var string
     */
    const EXTENSION = '.ini';

    /**
     * Instance of Zibo
     * @var zibo\core\Zibo
     */
    private $zibo;

    /**
     * Constructs a new ini translation IO
     * @param zibo\core\Zibo $zibo $zibo
     * @return null
     */
    public function __construct(Zibo $zibo) {
        $this->zibo = $zibo;
    }

    /**
     * Gets all the translations for the provided locale
     * @param string $localeCode code of the locale
     * @return array an associative array with translation key - value pairs
     * @throws zibo\ZiboException when the locale code is empty or invalid
     */
    public function getTranslations($localeCode) {
        if (!String::isString($localeCode, String::NOT_EMPTY)) {
            throw new ZiboException('Provided locale code is empty');
        }

        if (isset($this->translations[$localeCode])) {
            return $this->translations[$localeCode];
        }

        $this->translations[$localeCode] = array();

        $translationFile = Zibo::DIRECTORY_L10N . File::DIRECTORY_SEPARATOR . $localeCode . self::EXTENSION;
        $translationFiles = array_reverse($this->zibo->getFiles($translationFile));

        $this->translations[$localeCode] = $this->getTranslationsFromFiles($translationFiles);

        return $this->translations[$localeCode];
    }

    /**
     * Sets a translation for the provided locale
     * @param string $localeCode Code of the locale
     * @param string $key Key of the translation
     * @param string $translation
     * @return null
     * @throws zibo\ZiboException when the locale code is empty or invalid
     * @throws zibo\ZiboException when the translation key is empty or invalid
     * @throws zibo\ZiboException when the translation is empty or invalid
     */
    public function setTranslation($localeCode, $key, $translation) {
        if (!String::isString($localeCode, String::NOT_EMPTY)) {
            throw new ZiboException('Provided locale code is empty');
        }

        if (!String::isString($key, String::NOT_EMPTY)) {
            throw new ZiboException('Provided translation key is empty');
        }

        if (!String::isString($translation, String::NOT_EMPTY)) {
            throw new ZiboException('Provided translation is empty');
        }

        $translationFile = new File(Zibo::DIRECTORY_APPLICATION . File::DIRECTORY_SEPARATOR . Zibo::DIRECTORY_L10N, $localeCode . self::EXTENSION);

        if ($translationFile->exists()) {
            $translations = $this->getTranslationsFromFiles(array($translationFile));
        } else {
            $translations = array();
        }

        $translations[$key] = $translation;

        $this->setTranslationsToFile($translationFile, $translations);
    }

    /**
     * Reads the translations from the provided files
     * @param array $translationFiles Array with File objects of translation files
     * @return array Array with the translation key as array key and the translation as value
     */
    private function getTranslationsFromFiles($translationFiles) {
        $translations = array();

        foreach ($translationFiles as $translationFile) {
            $fileTranslations = parse_ini_file($translationFile->getPath(), false);
            if ($fileTranslations === false) {
                continue;
            }

            foreach ($fileTranslations as $key => $value) {
                $translations[$key] = $value;
            }
        }

        return $translations;
    }

    /**
     * Writes the provided translations to the provided file
     * @param zibo\library\filesystem\File $translationFile File to store the translations in
     * @param array $translations Array with the translation key as array key and the translation as value
     * @return null
     */
    private function setTranslationsToFile(File $translationFile, array $translations) {
        ksort($translations);

        $ini = '';
        foreach ($translations as $key => $translation) {
            $ini .= $key . ' = "' . str_replace('"', '\\"', $translation) . "\"\n";
        }

        $translationDirectory = $translationFile->getParent();
        $translationDirectory->create();

        $translationFile->write($ini);
    }

}