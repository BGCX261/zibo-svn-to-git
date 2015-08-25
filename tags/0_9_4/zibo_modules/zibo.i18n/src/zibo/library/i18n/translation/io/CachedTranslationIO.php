<?php

namespace zibo\library\i18n\translation\io;

use zibo\core\Zibo;

use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\Cache;
use zibo\library\cache\SimpleCache;
use zibo\library\filesystem\File;

/**
 * TranslationIO implementation which decorates another TranslationIO with a cache
 */
class CachedTranslationIO extends AbstractTranslationIO {

    /**
     * Path for the cache
     * @var string
     */
    const CACHE_PATH = 'application/data/cache/l10n';

    /**
     * Type for the cache objects
     * @var string
     */
    const CACHE_TYPE = 'translations';

    /**
     * Cache for the translations
     * @var zibo\library\cache\Cache
     */
    private $cache;

    /**
     * TranslationIO which is decorated by this class
     * @var zibo\library\i18n\translation\io\TranslationIO
     */
    private $io;

    /**
     * Constructs a new cached TranslationIO
     * @param zibo\library\i18n\translation\io\TranslationIO $io the Translation IO to cache
     * @return null
     */
    public function __construct(TranslationIO $io, Cache $cache = null) {
        $this->io = $io;
        $this->cache = $cache;
    }

    /**
     * Gets all the translations for the provided locale
     * @param string $localeCode code of the locale
     * @return array an associative array with translation key - value pairs
     */
    public function getAllTranslations($localeCode) {
        if (array_key_exists($localeCode, $this->translations)) {
            return $this->translations[$localeCode];
        }

        $cache = $this->getCache();
        $this->translations[$localeCode] = $cache->get(self::CACHE_TYPE, $localeCode);
        if ($this->translations[$localeCode]) {
            return $this->translations[$localeCode];
        }

        $this->translations[$localeCode] = $this->io->getAllTranslations($localeCode);

        $cache->set(self::CACHE_TYPE, $localeCode, $this->translations[$localeCode]);

        return $this->translations[$localeCode];
    }

    /**
     * Sets a translation for the provided locale
     * @param string $localeCode Code of the locale
     * @param string $key Key of the translation
     * @param string $translation
     * @return null
     */
    public function setTranslation($localeCode, $key, $translation) {
        $this->io->setTranslation($localeCode, $key, $translation);
    }

    /**
     * Gets the cache. It will be created on the fly when needed
     * @return zibo\library\cache\Cache
     */
    private function getCache() {
        if ($this->cache) {
            return $this->cache;
        }

        $cachePath = new File(self::CACHE_PATH);
        $cacheIO = new FileCacheIO($cachePath);
        $this->cache = new SimpleCache($cacheIO);

        return $this->cache;
    }

}