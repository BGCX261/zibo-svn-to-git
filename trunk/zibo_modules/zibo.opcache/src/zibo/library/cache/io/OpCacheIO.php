<?php

namespace zibo\library\cache\io;

/**
 * Interface for the opcode cache input/output
 */
interface OpCacheIO {

    /**
     * Increases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to increase with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function increase($key, $value = null, $timeToLive = null);

    /**
     * Decreases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to decrease with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function decrease($key, $value = null, $timeToLive = null);

    /**
     * Sets a value to the variable store
     * @param string $key The key of the variable
     * @param mixed $value The value of the variable
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return null
     */
    public function set($key, $value = null, $timeToLive = null);

    /**
     * Gets a value from the variable store
     * @param string $key The key of the variable
     * @param mixed $default The default value for when the key is not set
     * @return mixed The value of the variable if it exists, the provided default value otherwise
     */
    public function get($key, $default = null);

}