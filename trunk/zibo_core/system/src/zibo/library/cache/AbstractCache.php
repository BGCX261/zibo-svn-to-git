<?php

namespace zibo\library\cache;

use zibo\library\cache\io\CacheIO;
use zibo\library\cache\io\FileCacheIO;

/**
 * Abstract cache implementation
 */
abstract class AbstractCache implements Cache {

    /**
     * Input/output implementation of the cache
     * @var zibo\library\cache\io\CacheIO
     */
    protected $io;

    /**
     * Constructs a new cache
     * @param zibo\library\cache\io\CacheIO $io I/O implementation for this
     * cache, default is a file implementation in application/data/cache
     * @return null
     */
    public function __construct(CacheIO $io = null) {
        if (!$io) {
            $io = new FileCacheIO();
        }

        $this->io = $io;
    }

    /**
     * Clears a part or the full cache
     * @param string $type Type of the cache objects to remove, if empty the
     * complete cache is to be removed
     * @param string $id Id of the cache object to remove, if empty, all cache
     * objects of the provided type are to be removed
     * @return null
     */
    public function clear($type = null, $id = null) {
        $this->io->clearCache($type, $id);
    }

    /**
     * Gets a value from the cache
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object. If empty, all the cache objects
     * of the provided type are returned
     * @param mixed $default The return value if the cache object is not set
     * @return mixed Null if nothing was found. An array with the values if no
     * id was provided or the cached value otherwise
     */
    public function get($type, $id = null, $default = null) {
        $cacheObject = $this->io->readFromCache($type, $id);

        if ($cacheObject === null) {
            return $default;
        }

        if (is_array($cacheObject)) {
            return $this->processArray($cacheObject);
        }

        return $this->processObject($cacheObject);
    }

    /**
     * Processes the read for an array of cache objects
     * @param array $cacheObjects An array with cache objects
     * @return array Array with the values of the cache objects
     */
    protected function processArray(array $cacheObjects) {
        foreach ($cacheObjects as $key => $cacheObject) {
            $cacheObjects[$key] = $this->processObject($cacheObject);
        }

        return $cacheObjects;
    }

    /**
     * Processes the read of a cache object
     * @param CacheObject $cacheObject The cache object to process
     * @return mixed The value of the cache object
     */
    abstract protected function processObject(CacheObject $cacheObject = null);

}