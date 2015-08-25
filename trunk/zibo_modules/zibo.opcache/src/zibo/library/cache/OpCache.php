<?php

namespace zibo\library\cache;

use zibo\core\Zibo;

use zibo\library\cache\io\OpCacheIO;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Facade for a PHP opcode cache
 */
class OpCache {

    /**
     * Configuration key for the opcode cache IO implementation
     * @var string
     */
    const CONFIG_IO = 'system.opcache.io';

    /**
     * Class name of the opcode cache IO interface
     * @var string
     */
    const INTERFACE_IO = 'zibo\\library\\cache\\io\\OpCacheIO';

    /**
     * Suffix for the lock key
     * @var string
     */
    const LOCK_SUFFIX = '.lock';

    /**
     * Delay in microseconds between checking a locked variable
     * @var integer
     */
    const LOCK_DELAY = 1000;

    /**
     * Timeout when checking a locked variable
     * @var integer
     */
    const LOCK_TIMEOUT = 100000;

    /**
     * The opcode cache IO implementation
     * @var zibo\library\cache\io\OpCacheIO
     */
    protected $io;

    /**
     * Constructs a new opcode cache
     * @param zibo\library\cache\io\OpCacheIO $io The IO implementation, if none provided it will be created from the Zibo configuration
     * @return null
     */
    public function __construct(OpCacheIO $io = null) {
        if (!$io) {
            $io = self::createIOFromConfig();
        }

        $this->io = $io;
    }

    /**
     * Increases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to increase with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function increase($key, $value = null, $timeToLive = null) {
        return $this->io->increase($key, $value, $timeToLive);
    }

    /**
     * Decreases the value in the variable store
     * @param string $key The key of the variable
     * @param integer $value The value to decrease with
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return mixed The new value of the variable
     */
    public function decrease($key, $value = null, $timeToLive = null) {
        return $this->io->decrease($key, $value, $timeToLive);
    }

    /**
     * Sets a value to the variable store
     * @param string $key The key of the variable
     * @param mixed $value The value of the variable
     * @param integer $timeToLive Set to a number of seconds to make the variable expire in that amount of time
     * @return null
     */
    public function set($key, $value = null, $timeToLive = null) {
        $this->io->set($key, $value, $timeToLive);
    }

    /**
     * Gets a value from the variable store
     * @param string $key The key of the variable
     * @param mixed $default The default value for when the key is not set
     * @param boolean $checkLock Flag to see if this method should wait until the requested variable is unlocked
     * @return mixed The value of the variable if it exists, the provided default value otherwise
     */
    public function get($key, $default = null, $checkLock = false) {
        if (!$checkLock) {
            return $this->io->get($key, $default);
        }

        $lockKey = $this->getLockKey($key);

        $timeSlept = 0;
        while ($this->io->get($lockKey) && $timeSlept < self::LOCK_TIMEOUT) {
            usleep(self::LOCK_DELAY);
            $timeSlept += self::LOCK_DELAY;
        }

        if ($timeSlept >= self::LOCK_TIMEOUT) {
            return $default;
        }

        return $this->io->get($key, $default);
    }

    /**
     * Locks the variable with the provided key
     * @param string $key The key of the variable
     * @return null
     */
    public function lock($key) {
        $lockKey = $this->getLockKey($key);
        $this->io->set($lockKey, 1);
    }

    /**
     * Unlocks the variable with the provided key
     * @param string $key The key of the variable
     * @return null
     */
    public function unlock($key) {
        $lockKey = $this->getLockKey($key);
        $this->io->set($lockKey);
    }

    /**
     * Gets the key for the lock variable of the provided key
     * @param string $key The key to get the lock key of
     * @return string The key of the lock variable
     */
    protected function getLockKey($key) {
        return $key . self::LOCK_SUFFIX;
    }

    /**
     * Creates the opcode cache i/o from the Zibo configuration
     * @return zibo\library\cache\io\OpCacheIO
     */
    public static function createIOFromConfig() {
        $class = Zibo::getInstance()->getConfigValue(self::CONFIG_IO);
        if (!$class) {
            throw new ZiboException('No opcode cache implementation set. Please set the ' . self::CONFIG_IO . ' configuration value.');
        }

        $objectFactory = new ObjectFactory();
        return $objectFactory->create($class, self::INTERFACE_IO);
    }

}