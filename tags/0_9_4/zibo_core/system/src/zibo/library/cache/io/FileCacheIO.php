<?php

namespace zibo\library\cache\io;

use zibo\core\Zibo;

use zibo\library\cache\CacheObject;
use zibo\library\filesystem\File;

use \Exception;

/**
 * File implementation for the cache input/output. For each cache type, there will be a directory.
 * Each cache object will be stored in a file in the directory of it's cache type.
 */
class FileCacheIO implements CacheIO {

    /**
     * Default base path for the cache files
     * @var string
     */
    const DEFAULT_PATH = 'application/data/cache/object';

    /**
     * Base path of the cache
     * @var zibo\library\filesystem\File
     */
    private $path;

    /**
     * Flag to see if file locking should be used
     * @var boolean
     */
    private $useLock;

    /**
     * Constructs a new file cache i/o implementation
     * @param zibo\library\filesystem\File $path Base path for the cache files
     * @param boolean $useLock Flag to see if file locking should be used
     * @return null
     */
    public function __construct(File $path = null, $useLock = false) {
        if ($path === null) {
            $path = new File(self::DEFAULT_PATH);
        }

        $this->path = $path;
        $this->useLock = $useLock;
    }

    /**
     * Writes a cache object to file
     * @param zibo\library\cache\CacheObject $object Cache object to write to the data source
     * @return null
     */
    public function writeToCache(CacheObject $object) {
        $cacheFile = $this->getCacheFile($object->getType(), $object->getId());

        $cacheDirectory = $cacheFile->getParent();
        $cacheDirectory->create();

        $serializedValue = serialize($object);

        if ($this->useLock) {
            $cacheFile->lock();
            $cacheFile->write($serializedValue);
            $cacheFile->unlock();
        } else {
            $cacheFile->write($serializedValue);
        }
    }

    /**
     * Reads a cache object or cache objects from the filesystem
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object, if empty, all cache objects from the provided type are requested
     * @return null|array|zibo\library\cache\CacheObject A cache object or an array of cache objects with the id as key. Null if nothing was found
     */
    public function readFromCache($type, $id = null) {
        if (is_null($id)) {
            return $this->readPathFromCache($type);
        }

        $cacheFile = $this->getCacheFile($type, $id);

        return $this->readCacheObject($cacheFile);
    }

    /**
     * Reads the cache objects for the provided type
     * @param string $type Type of the cache objects
     * @return array Array with the cache objects for the provided type, the cache id is the key of the array
     */
    private function readPathFromCache($type) {
        $cacheObjects = array();

        $cachePath = $this->getDirectoryFile($type);
        if (!$cachePath->exists()) {
            return $cacheObjects;
        }

        $cacheFiles = $cachePath->read();
        foreach ($cacheFiles as $cacheFile) {
            $cacheObjects[$cacheFile->getName()] = $this->readCacheObject($cacheFile);
        }

        return $cacheObjects;
    }

    /**
     * Clears a part or the full cache
     * @param string $type Type of the cache objects to remove, if empty the complete cache is to be removed
     * @param string $id Id of the cache object to remove, if empty, all cache objects of the provided type are to be removed
     * @return null
     */
    public function clearCache($type = null, $id = null) {
        if (is_null($type)) {
            $cachePath = $this->getDirectoryFile(null);
            if (!$cachePath->exists()) {
                return;
            }

            $files = $cachePath->read();
            foreach ($files as $file) {
                $file->delete();
            }

            return;
        }

        if (is_null($id)) {
            $cacheFile = $this->getDirectoryFile($type);
        } else {
            $cacheFile = $this->getCacheFile($type, $id);
        }

        if ($cacheFile->exists()) {
            $cacheFile->delete();
        }
    }

    /**
     * Reads the cache object from a file
     * @param zibo\library\filesystem\File $cacheFile File of the cache object
     * @return zibo\library\cache\CacheObject|null The read cache object or null
     */
    private function readCacheObject(File $cacheFile) {
        if (!$cacheFile->exists()) {
            return null;
        }

        try {
            if ($this->useLock) {
                $cacheFile->waitForUnlock();
            }

            $serializedValue = $cacheFile->read();
            $object = unserialize($serializedValue);
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
            $object = null;
        }

        if (!$object) {
            return null;
        }

        return $object;
    }

    /**
     * Gets the file for the provided cache object
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object
     * @return zibo\library\filesystem\File File of the cache object
     */
    private function getCacheFile($type, $id) {
        $directory = $this->getDirectoryFile($type);
        return new File($directory, $id);
    }

    /**
     * Gets the directory for the provided cache type
     * @param string $type Type of cache objects
     * @return zibo\library\filesystem\File The directory for the provided cache type
     */
    private function getDirectoryFile($type) {
        return new File($this->path, $type);
    }

}