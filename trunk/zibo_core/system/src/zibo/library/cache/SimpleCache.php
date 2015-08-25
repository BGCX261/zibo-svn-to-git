<?php

namespace zibo\library\cache;

/**
 * Simple cache implementation
 */
class SimpleCache extends AbstractCache {

    /**
     * Store a value in the cache
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object
     * @param mixed $value The value to cache
     * @return null
     */
    public function set($type, $id, $value) {
        $cacheObject = new CacheObject($type, $id, $value);
        $this->io->writeToCache($cacheObject);
    }

    /**
     * Processes the read of a cache object
     * @param CacheObject $cacheObject The cache object to process
     * @return mixed The value of the cache object
     */
    protected function processObject(CacheObject $cacheObject = null) {
        if (!$cacheObject) {
            return null;
        }

        return $cacheObject->getData();
    }

}