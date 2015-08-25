<?php

namespace zibo\library\cache;

/**
 * Interface for a cache
 */
interface Cache {

    /**
     * Clears a part or the full cache
     * @param string $type Type of the cache objects to remove, if empty the complete cache is to be removed
     * @param string $id Id of the cache object to remove, if empty, all cache objects of the provided type are to be removed
     * @return null
     */
    public function clear($type = null, $id = null);

    /**
     * Store a value in the cache
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object
     * @param mixed $value The value to cache
     * @return null
     */
    public function set($type, $id, $value);

    /**
     * Gets a value from the cache
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object. If empty, all the cache objects of the provided type are returned
     * @param mixed $default The return value if the cache object is not set
     * @return mixed Null if nothing was found. An array with the values if no id was provided or the cached value otherwise
     */
    public function get($type, $id = null, $default = null);

}