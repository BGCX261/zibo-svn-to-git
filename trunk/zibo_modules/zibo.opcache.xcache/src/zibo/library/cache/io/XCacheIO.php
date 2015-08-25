<?php

namespace zibo\library\cache\io;

use zibo\ZiboException;

/**
 * XCache implementation for the opcode cache input/output
 */
class XCacheIO implements OpCacheIO {

    /**
     * Constructs a new XCache facade
     * @return null
     * @throws zibo\ZiboException when the xcache functions are not available
     */
    public function __construct() {
        if (!function_exists('xcache_get')) {
            throw new ZiboException('Could not create the XCache implementation. XCache is not installed or not enabled.');
        }
    }

    /**
     * Increases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to increase with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function increase($key, $value = null, $timeToLive = null) {
        return xcache_inc($key, $value, $timeToLive);
    }

    /**
     * Decreases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to decrease with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function decrease($key, $value = null, $timeToLive = null) {
        return xcache_dec($key, $value, $timeToLive);
    }

    /**
     * Sets a value to the variable store
     * @param string $key The key of the variable
     * @param mixed $value The value of the variable
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return null
     */
    public function set($key, $value = null, $timeToLive = null) {
        if ($value === null) {
            xcache_unset($key);
        } else {
            xcache_set($key, $value, $timeToLive);
        }
    }

    /**
     * Gets a value from the variable store
     * @param string $key The key of the variable
     * @param mixed $default The default value for when the key is not set
     * @return mixed The value of the variable if it exists, the provided default value otherwise
     */
    public function get($key, $default = null) {
        if (xcache_isset($key)) {
            return xcache_get($key);
        }

        return $default;
    }

}