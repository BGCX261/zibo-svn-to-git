<?php

namespace zibo\library\cache;

use zibo\library\cache\io\CacheIO;
use zibo\library\Number;

use zibo\ZiboException;

/**
 * Extended cache implementation
 */
class ExtendedCache extends AbstractCache {

    /**
     * Number of accesses before a cached object is cleared
     * @var int
     */
    private $cleanUpTimes;

    /**
     * Age in seconds before a cached object is cleared
     * @var int
     */
    private $cleanUpAge;

    /**
     * Constructs a new cache
     * @param zibo\library\cache\io\CacheIO $io I/O implementation for this
     * cache, default is a file implementation in application/data/cache
     * @return null
     */
    public function __construct(CacheIO $io = null) {
        parent::__construct($io);

        $this->cleanUpTimes = null;
        $this->cleanUpAge = null;
    }

    /**
     * Sets how many times a cache object can be accessed before it's cleared.
     * @param int $times Number of times a cache object can be accessed before
     * it's cleared. Set to null or 0 to skip the access number check
     * @return null
     * @throws zibo\ZiboException when the provided times is invalid
     */
    public function setCleanUpTimes($times) {
        if ($times === null || $times === 0) {
            $this->cleanUpTimes = null;
            return;
        }

        if (!Number::isNumeric($times, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new ZiboException('Provided clean up times is negative or a float');
        }

        $this->cleanUpTimes = $times;
    }

    /**
     * Gets the number of times a cache object can be accessed before it's cleared
     * @return int
     */
    public function getCleanUpTimes() {
        return $this->cleanUpTimes;
    }

    /**
     * Sets how old a cache object may become before it's cleared.
     * @param int $age Number of seconds a cache object can exist before it's
     * cleared. Set to null or 0 to skip age checking
     * @return null
     * @throws zibo\ZiboException when the provided age is invalid
     */
    public function setCleanUpAge($age) {
        if ($age === null || $age === 0) {
            $this->cleanUpAge = null;
            return;
        }

        if (!Number::isNumeric($age, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new ZiboException('Provided clean up age is negative');
        }

        $this->cleanUpAge = $age;
    }

    /**
     * Gets how old a cache object may become before it's cleared.
     * @return int Age in seconds
     */
    public function getCleanUpAge() {
        return $this->cleanUpAge;
    }

    /**
     * Checks if the provided cache objects needs to be cleared
     * @param CacheObject $object Cache object to check
     * @return boolean True if the cache objects needs to be cleared,
     * false otherwise
     */
    private function needCleanUp(CacheObject $object) {
        $cleanUpTimes = $this->getCleanUpTimes();
        if ($cleanUpTimes && $object->getTimesAccessed() >= $cleanUpTimes) {
            return true;
        }

        $cleanUpAge = $this->getCleanUpAge();
        if ($cleanUpAge) {
            $objectAge = $object->getLastAccessDate() - $object->getCreationDate();
            if ($objectAge >= $cleanUpAge) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store a value in the cache
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object
     * @param mixed $value The value to cache
     * @return null
     */
    public function set($type, $id, $value) {
        $cacheObject = $this->io->readFromCache($type, $id);

        if (!$cacheObject) {
            $cacheObject = new CacheObject($type, $id, $value);
        } else {
            $cacheObject->setData($value);
        }

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

        $cacheObject->access();

        if ($this->needCleanUp($cacheObject)) {
            $this->io->clearCache($cacheObject->getType(), $cacheObject->getId());
        } else {
            $this->io->writeToCache($cacheObject);
        }

        return $cacheObject->getData();
    }

}