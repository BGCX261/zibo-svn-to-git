<?php

namespace zibo\library\http\session;

/**
 * Interface for a session container
 */
interface Session {

    /**
     * Gets the id of the session
     * @return string Id of the session
     */
    public function getId();

    /**
     * Loads a previous session by it's id
     * @param string $id Id of a previous session
     * @return null
     */
    public function load($id);

    /**
     * Get a value from the session
     * @param string $key key of the value
     * @param mixed $default default value for when the key is not set
     * @return mixed the stored session value, if it does not exist you will get
     * the provided default value
     */
    public function get($key, $default = null);

    /**
     * Sets a value to the session or clear a previously set key by passing a
     * null value
     * @param string $key Key of the value
     * @param mixed $value The value, null to clear
     * @return null
     */
    public function set($key, $value = null);

    /**
     * Clears all values in the session
     * @return null
     */
    public function reset();

}