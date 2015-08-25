<?php

namespace zibo\library;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

use zibo\ZiboException;

/**
 * Facade to the session
 */
class Session {

    /**
     * Configuration key for the divisor of the garbage collector
     * @var string
     */
    const CONFIG_SESSION_GC_DIVISOR = 'system.session.gc.divisor';

    /**
     * Configuration key for the probability of the garbage collector
     * @var string
     */
    const CONFIG_SESSION_GC_PROBABILITY = 'system.session.gc.probability';

    /**
     * Configuration key for the path of the sessions
     * @var string
     */
    const CONFIG_SESSION_PATH = 'system.session.path';

    /**
     * Configuration key of the session time
     * @var string
     */
    const CONFIG_SESSION_TIME = 'system.session.time';

    /**
     * Default value for the divisor of the garbage collector
     * @var int
     */
    const DEFAULT_SESSION_GC_DIVISOR = 1;

    /**
     * Default value for the probability of the garbage collector
     * @var int
     */
    const DEFAULT_SESSION_GC_PROBABILITY = 1;

    /**
     * Default value for the path of the sessions
     * @var string
     */
    const DEFAULT_SESSION_PATH = 'application/data/session';

    /**
     * Default life time of a session (in minutes)
     * @var int
     */
    const DEFAULT_SESSION_TIME = 30;

    /**
     * Key in $_SESSION of the array which contains the values of this Session object
     * @var string
     */
    const SESSION_NAME = 'zibo';

    /**
     * Instance of the session object
     * @var Session
     */
    private static $instance = null;

    /**
     * Construct the session
     * @return null
     */
    private function __construct() {
        $this->initialize();
    }

    /**
     * Clone the session (not allowed)
     * @return null
     * @throws ZiboException when trying to clone this object
     */
    public function __clone() {
        throw new ZiboException('Cannot clone this object');
    }

    /**
     * Get the instance of the session
     * @return Session instance of the session manager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the id of the current session
     * @return string id of the current session
     */
    public function getId() {
        return session_id();
    }

    /**
     * Gets the path where the session files are stored
     * @return zibo\library\filesystem\File
     */
    public function getPath() {
        return new File(ini_get('session.save_path'));
    }

    /**
     * Gets the maximum lifetime of a session
     * @return integer Lifetime of a session in seconds
     */
    public function getLifeTime() {
        return ini_get('session.gc_maxlifetime');
    }

    /**
     * Get a value from the current session
     * @param string $key key of the value
     * @param mixed $default default value for when the key is not set
     * @return mixed the stored session value, if it does not exist you will get the passed default value
     */
    public function get($key, $default = null) {
        $result = $default;

        if (array_key_exists($key, $_SESSION[self::SESSION_NAME])) {
            $result = $_SESSION[self::SESSION_NAME][$key];
        }

        return $result;
    }

    /**
     * Set a value to the current session or clear a previously set key by passing a null value
     * @param string $key key of the value
     * @param mixed $value value for the key
     * @return null
     */
    public function set($key, $value = null) {
        if ($value === null) {
            if (array_key_exists($key, $_SESSION[self::SESSION_NAME])) {
                unset($_SESSION[self::SESSION_NAME][$key]);
            }
        } else {
            $_SESSION[self::SESSION_NAME][$key] = $value;
        }
    }

    /**
     * Clear all values in the current session
     * @return null
     */
    public function reset() {
        $_SESSION[self::SESSION_NAME] = array();
    }

    /**
     * Reload a previous session
     * @param string $sessionId session id of a previous session
     * @return null
     */
    public function load($sessionId) {
        @session_write_close();

        session_id($sessionId);

        $this->startSession();
    }

    /**
     * Initialize the session configuration and start the session
     * @return null
     */
    private function initialize() {
        $zibo = Zibo::getInstance();

        $sessionTime = $zibo->getConfigValue(self::CONFIG_SESSION_TIME, self::DEFAULT_SESSION_TIME) * 60;
        $sessionProbability = $zibo->getConfigValue(self::CONFIG_SESSION_GC_PROBABILITY, self::DEFAULT_SESSION_GC_PROBABILITY);
        $sessionDivisor = $zibo->getConfigValue(self::CONFIG_SESSION_GC_DIVISOR, self::DEFAULT_SESSION_GC_DIVISOR);
        $sessionPath = $zibo->getConfigValue(self::CONFIG_SESSION_PATH, self::DEFAULT_SESSION_PATH);

        $fileSessionPath = new File($sessionPath);
        $fileSessionPath->create();

        ini_set('session.gc_maxlifetime', $sessionTime);
        ini_set('session.gc_probability', $sessionProbability);
        ini_set('session.gc_divisor', $sessionDivisor);
        ini_set('session.save_path', $sessionPath);

        $this->startSession();

        if (!array_key_exists(self::SESSION_NAME, $_SESSION)) {
            $_SESSION[self::SESSION_NAME] = array();
        }
    }

    /**
     * Start the session
     * @return null
     */
    private function startSession() {
        if (!@session_start()) {
            $error = error_get_last();
            throw new ZiboException('Could not start the session: ' . $error['message']);
        }

        if (!isset($_SESSION[self::SESSION_NAME])) {
            $this->reset();
        }
    }

}