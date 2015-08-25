<?php

namespace zibo\library\cache\io;

use zibo\library\cache\CacheObject;

/**
 * Interface for a cache input-output implementation
 */
interface CacheIO {

    /**
     * Reads a cache object or cache objects from the cache data source
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object, if empty, all cache objects from the provided type are requested
     * @return null|array|zibo\library\cache\CacheObject A cache object or an array of cache objects with the id as key. Null if nothing was found
     */
    public function readFromCache($type, $id = null);

    /**
     * Clears a part or the full cache
     * @param string $type Type of the cache objects to remove, if empty the complete cache is to be removed
     * @param string $id Id of the cache object to remove, if empty, all cache objects of the provided type are to be removed
     * @return null
     */
    public function clearCache($type = null, $id = null);

    /**
     * Writes a cache object to the data source
     * @param zibo\library\cache\CacheObject $object Cache object to write to the data source
     * @return null
     */
    public function writeToCache(CacheObject $object);

}
