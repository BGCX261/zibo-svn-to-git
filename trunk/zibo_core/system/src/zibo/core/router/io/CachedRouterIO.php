<?php

namespace zibo\core\router\io;

use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\Cache;
use zibo\library\cache\SimpleCache;
use zibo\library\filesystem\File;

/**
 * Cache decorator for another RouterIO
 */
class CachedRouterIO extends AbstractRouterIO {

    /**
     * Path of the router cache
     * @var string
     */
    const CACHE_PATH = 'application/data/cache';

    /**
     * Cache type for the cache objects
     * @var string
     */
    const CACHE_TYPE = 'router';

    /**
     * Cache id for the routes
     * @var string
     */
    const CACHE_ID_ALIASES = 'aliases';

    /**
     * Cache id for the routes
     * @var string
     */
    const CACHE_ID_ROUTES = 'routes';

    /**
     * The cache used by this router
     * @var unknown_type
     */
    private $cache;

    /**
     * RouterIO which is cached by this RouterIO
     * @var zibo\core\router\io\RouterIO
     */
    private $io;

    /**
     * Constructs a new cached RouterIO
     * @param RouterIO $io the RouterIO which needs a cache
     * @param zibo\library\cache\Cache $cache the cache to use (optional)
     * @return null
     */
    public function __construct(RouterIO $io, Cache $cache = null) {
        $this->io = $io;
        $this->cache = $cache;
    }

    /**
     * Clears the cache
     * @return null
     */
    public function clearCache() {
        $this->getCache()->clear(self::CACHE_TYPE, self::CACHE_ID_ALIASES);
        $this->getCache()->clear(self::CACHE_TYPE, self::CACHE_ID_ROUTES);
    }

    /**
    * Reads the aliases from the data source
    * @return array Array with the path as key and the a Alias object as value
    */
    protected function readAliases() {
    	$cache = $this->getCache();

    	$aliases = $cache->get(self::CACHE_TYPE, self::CACHE_ID_ALIASES);
    	if ($aliases) {
    		return $aliases;
    	}

    	$aliases = $this->io->getAliases();

    	$cache->set(self::CACHE_TYPE, self::CACHE_ID_ALIASES, $aliases);

    	return $aliases;
    }

    /**
     * Reads the routes from the data source
     * @return array Array with Route instances
     */
    protected function readRoutes() {
        $cache = $this->getCache();

        $routes = $cache->get(self::CACHE_TYPE, self::CACHE_ID_ROUTES);
        if ($routes) {
            return $routes;
        }

        $routes = $this->io->getRoutes();

        $cache->set(self::CACHE_TYPE, self::CACHE_ID_ROUTES, $routes);

        return $routes;
    }

    /**
     * Gets the cache which is used by this RouterIO
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