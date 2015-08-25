<?php

namespace zibo\library\http\session;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Native PHP implementation of the session container
 */
class PhpSession implements Session {

    /**
     * Constructs a new session
     * @return null
     */
    public function __construct() {
        $this->startSession();
    }

    /**
     * Gets the id of the session
     * @return string Id of the session
     */
    public function getId() {
        return session_id();
    }

    /**
     * Loads a previous session by it's id
     * @param string $id Id of a previous session
     * @return null
     * @throws zibo\ZiboException when an invalid id has been provided
     */
    public function load($id) {
        if (!String::isString($id, String::NOT_EMPTY)) {
            throw new ZiboException('Provided id is empty or invalid');
        }

        @session_write_close();

        session_id($id);

        $this->startSession();
    }

    /**
     * Get a value from the session
     * @param string $key key of the value
     * @param mixed $default default value for when the key is not set
     * @return mixed the stored session value, if it does not exist you will get
     * the provided default value
     * @throws zibo\ZiboException when the provided key is empty or invalid
     */
    public function get($key, $default = null) {
        if (!String::isString($key, String::NOT_EMPTY)) {
            throw new ZiboException('Provided key is empty or invalid');
        }

        if (!isset($_SESSION[$key])) {
            return $default;
        }

        return $_SESSION[$key];
    }

    /**
     * Sets a value to the session or clear a previously set key by passing a
     * null value
     * @param string $key Key of the value
     * @param mixed $value The value, null to clear
     * @return null
     * @throws zibo\ZiboException when an invalid key is provided
     */
    public function set($key, $value = null) {
        if (!String::isString($key, String::NOT_EMPTY)) {
            throw new ZiboException('Provided id is empty or invalid');
        }

        if ($value === null) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        } else {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Clears all values in the session
     * @return null
     */
    public function reset() {
        session_destroy();
        $_SESSION = array();
    }

    /**
     * Starts the session
     * @return null
     */
    private function startSession() {
        if (!@session_start()) {
            $error = error_get_last();
            throw new ZiboException('Could not start the session: ' . $error['message']);
        }
    }

}