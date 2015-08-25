<?php

namespace zibo\library\cache\io;

use zibo\ZiboException;

/**
 * APC implementation for the opcode cache input/output
 */
class ApcIO implements OpCacheIO {

    /**
     * Constructs a new XCache facade
     * @return null
     * @throws zibo\ZiboException when the xcache functions are not available
     */
    public function __construct() {
        if (!function_exists('apc_fetch')) {
            throw new ZiboException('Could not create the APC implementation. APC is not installed or not enabled.');
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
        if ($value === null) {
            $value = 1;
        }

        // make sure the variable exists
        apc_add($key, 0);

        return apc_inc($key, $value);
    }

    /**
     * Decreases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to decrease with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function decrease($key, $value = null, $timeToLive = null) {
        if ($value === null) {
            $value = 1;
        }

        // make sure the variable exists
        apc_add($key, 0);

        return apc_dec($key, $value);
    }

    /**
     * Sets a value to the variable store
     * @param string $key The key of the variable
     * @param mixed $value The value of the variable
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return null
     */
    public function set($key, $value = null, $timeToLive = null) {
        if ($value === null && !is_array($key)) {
            apc_add($key, 0);
            apc_delete($key);
        } else {
            apc_store($key, $value, $timeToLive);
        }
    }

    /**
     * Gets a value from the variable store
     * @param string $key The key of the variable
     * @param mixed $default The default value for when the key is not set
     * @return mixed The value of the variable if it exists, the provided default value otherwise
     */
    public function get($key, $default = null) {
        $value = apc_fetch($key, $success);
        if (!$success) {
            $value = $default;
        }

        return $value;
    }

}