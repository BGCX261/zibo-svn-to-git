<?php

namespace zibo\library\config\io;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\Cache;
use zibo\library\cache\ExtendedCache;
use zibo\library\config\Config;
use zibo\library\filesystem\File;
use zibo\library\Boolean;

/**
 * Decorator for another ConfigIO implementation with the ability to cache the configuration
 */
class CachedConfigIO implements ConfigIO {

    /**
     * Configuration key for the cache enabled flag. Needs to be prefixed with the config section.
     * @var string
     */
    const CONFIG_CACHE_ENABLED = 'cache.enabled';

    /**
     * Configuration key for the times a cache object may be used. Needs to be prefixed with the config section.
     * @var string
     */
    const CONFIG_CACHE_TIMES = 'cache.times';

    /**
     * Configuration key for the age a cache object may become. Needs to be prefixed with the config section.
     * @var string
     */
    const CONFIG_CACHE_AGE = 'cache.age';

    /**
     * The path for the cache
     * @var string
     */
    const CACHE_PATH = 'application/data/cache/config';

    /**
     * Configuration key for the configuration of the config implementation
     * @var string
     */
    const SECTION_CONFIG = 'config';

    /**
     * Hierarchic configuration array from the 'config' section, with each configuration token as a key
     * @var array
     */
    private $config;

    /**
     * The ConfigIO implementation to decorate
     * @var ConfigIO
     */
    protected $configIO;

    /**
     * Cache object to use if caching is enabled
     * @var zibo\library\cache\Cache
     */
    protected $cache;

    /**
     * Type used to cache, equal to the environment name so each environment can have a different configuration
     * @var string
     */
    protected $cacheType;

    /**
     * Flag to enable/disable the cache
     * @var boolean
     */
    protected $isCacheEnabled;

    /**
     * Construct a new cached ConfigIO
     *
     * @param zibo\core\environment\Environment $environment the environment currently operating in
     * @param ConfigIO $configIO the ConfigIO implementation to decorate
     * @param zibo\library\cache\Cache $cache the Cache to use for caching, omit it to use the default
     *                                        file based cache at the path application/data/cache/config
     */
    public function __construct(Environment $environment, ConfigIO $configIO, Cache $cache = null) {
        $this->configIO = $configIO;
        $this->cacheType = $environment->getName();
        $this->cache = $cache;
        $this->isCacheEnabled = null;
    }

    /**
     * Enable or disable the cache
     *
     * When caching is disabled, all calls will be passed directly to the ConfigIO implementation it is
     * decorating.
     *
     * @param mixed $flag a boolean, true enables the cache, false disables it
     * @return null
     */
    public function setIsCacheEnabled($flag) {
        $this->isCacheEnabled = Boolean::getBoolean($flag);
    }

    /**
     * Find out if the cache is enabled or not
     *
     * @return boolean true if the cache is enabled, false if not
     */
    public function isCacheEnabled() {
        if ($this->isCacheEnabled === null) {
            $this->isCacheEnabled = $this->getConfigValue(self::CONFIG_CACHE_ENABLED, false);
        }
        if ($this->isCacheEnabled && $this->cache === null) {
            $this->cache = $this->createCache();
        }

        return $this->isCacheEnabled;
    }

    /**
     * Clear the cache
     */
    public function clearCache() {
        if ($this->cache) {
            $this->cache->clear($this->cacheType);
        }
    }

    /**
     * Read a section from the configuration
     * @param string $section
     * @return array Hierarchic array with each configuration token as a key
     */
    public function read($section) {
        if (!$this->isCacheEnabled()) {
            return $this->configIO->read($section);
        }

        $cached = $this->cache->get($this->cacheType, $section);
        if ($cached !== null) {
            return $cached;
        }

        $values = $this->configIO->read($section);

        if ($section !== self::SECTION_CONFIG) {
            $this->cache->set($this->cacheType, $section, $values);
        }

        return $values;
    }

    /**
     * Write a configuration value
     *
     * @param string $key
     * @param mixed $value
     * @return null
     *
     * @throws zibo\library\config\exception\ConfigException when the provided key is invalid or empty
     */
    public function write($key, $value) {
        if (!$this->isCacheEnabled()) {
            return $this->configIO->write($key, $value);
        }

        $tokens = explode(Config::TOKEN_SEPARATOR, $key);
        $section = $tokens[0];
        $this->cache->clear($this->cacheType, $section);

        $this->configIO->write($key, $value);
    }

    /**
     * Read the complete configuration
     *
     * @return array Hierarchic array with each configuration token as a key
     */
    public function readAll() {
       if (!$this->isCacheEnabled()) {
           return $this->configIO->readAll();
       }

       $all = $this->cache->get($this->cacheType);

       $sections = $this->getAllSections();
       foreach ($sections as $section) {
           if (!array_key_exists($section, $all))
           $all[$section] = $this->read($section);
       }

       return $all;
    }

   /**
    * Get the names of all the sections in the configuration
    *
    * @return array Array with the names of of all the sections in the configuration
    */
    public function getAllSections() {
       if (!$this->isCacheEnabled()) {
           return $this->configIO->getAllSections();
       }

        $all = $this->cache->get($this->cacheType);
        $sections = array_merge(array_keys($all), $this->configIO->getAllSections());
        $sections = array_unique($sections);
        return array_values($sections);
    }

    /**
     * Create the default Cache object, in case no Cache argument has been passed to __construct()
     *
     * @return zibo\library\cache\Cache a Cache object configured with a FileCacheIO at the path application/data/cache/config
     */
    private function createCache() {
        $cacheAge = $this->getConfigValue(self::CONFIG_CACHE_AGE);
        $cacheTimes = $this->getConfigValue(self::CONFIG_CACHE_TIMES);
        $isCacheEnabled = $this->getConfigValue(self::CONFIG_CACHE_ENABLED, false);

        $cachePath = new File(self::CACHE_PATH);
        $cacheIO = new FileCacheIO($cachePath);
        $this->setIsCacheEnabled($isCacheEnabled);

        $cache = new ExtendedCache($cacheIO);
        $cache->setCleanUpAge($cacheAge);
        $cache->setCleanUpTimes($cacheTimes);

        Zibo::getInstance()->registerEventListener(Zibo::EVENT_CLEAR_CACHE, array($this, 'clearCache'));

        return $cache;
    }

    /**
     * Get a configuration value from the 'config' section
     *
     * @param string $key the key fo the configuration value
     * @param mixed $default default value to use if the configuration value is missing
     * @return mixed the configuration value
     * @see getConfig()
     */
    private function getConfigValue($key, $default = null) {
        $tokens = explode(Config::TOKEN_SEPARATOR, $key);

        $this->getConfig();

        $result = $this->config;
        foreach ($tokens as $token) {
            if (!isset($result[$token])) {
                return $default;
            }
            $result = $result[$token];
        }

        return $result;
    }

    /**
     * Read the configuration for the cache itself into the member variable $this->config
     *
     * This always bypasses the cache so changes of the configuration of the config system itself
     * take effect immediately without the need to clear the cache.
     *
     * @return array Hierarchic configuration array from the 'config' section, with each configuration token as a key
     */
    private function getConfig() {
        if ($this->config !== null) {
            return;
        }

        $this->config = $this->configIO->read(self::SECTION_CONFIG);
    }

}