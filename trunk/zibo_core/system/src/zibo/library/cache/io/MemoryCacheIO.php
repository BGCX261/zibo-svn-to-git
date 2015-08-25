<?php

namespace zibo\library\cache\io;

use zibo\library\cache\CacheObject;
use zibo\library\filesystem\File;

use \Exception;

/**
 * Memory implementation for the cache input/output. All cache objects are
 * stored in 1 file which is read when the cache is first accessed and written
 * when the cache destructs.
 */
class MemoryCacheIO implements CacheIO {

    /**
     * The default file for this cache io
     * @var string
     */
    const DEFAULT_FILE = 'application/data/cache/memory.cache';

    /**
     * The file to store the values in
     * @var zibo\library\filesystem\File
     */
    private $file;

    /**
     * The values of this cache
     * @var array
     */
    private $cache;

    /**
     * Constructs a new memory cache io
     * @param zibo\library\filesystem\File $file The file to store the values to
     * @return null
     */
    public function __construct(File $file = null) {
        if ($file === null) {
            $file = new File(self::DEFAULT_FILE);
        }
        $this->file = $file;
        $this->cache = null;
    }

    /**
     * Writes the cache values to the file
     * @return null
     */
    public function __destruct() {
        try {
            $this->writeFile();
        } catch (Exception $exception) {
            echo $exception->getMessage() . "<br />\n" . $exception->getTraceAsString();
        }
    }

    /**
     * Reads a cache object or cache objects from the cache data source
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object, if empty, all cache objects
     * from the provided type are requested
     * @return null|array|zibo\library\cache\CacheObject A cache object or an
     * array of cache objects with the id as key. Null if nothing was found
     */
    public function readFromCache($type, $id = null) {
        $this->readFile();

        if ($id == null) {
            if (!isset($this->cache[$type])) {
                return array();
            }
            return $this->cache[$type];
        } elseif (!isset($this->cache[$type][$id])) {
            return null;
        }

        return $this->cache[$type][$id];
    }

    /**
     * Writes a cache object to the data source
     * @param zibo\library\cache\CacheObject $object Cache object to write to
     * the data source
     * @return null
     */
    public function writeToCache(CacheObject $object) {
        $this->readFile();

        $type = $object->getType();
        $id = $object->getId();

        if (!isset($this->cache[$type])) {
            $this->cache[$type] = array();
        }

        $this->cache[$type][$id] = $object;
    }

    /**
     * Clears a part or the full cache
     * @param string $type Type of the cache objects to remove, if empty the complete cache is to be removed
     * @param string $id Id of the cache object to remove, if empty, all cache objects of the provided type are to be removed
     * @return null
     */
    public function clearCache($type = null, $id = null) {
        if (is_null($type)) {
            $this->cache = array();
            return;
        }

        $this->readFile();

        if (is_null($id)) {
            if (isset($this->cache[$type])) {
                unset($this->cache[$type]);
            }
            return;
        }

        if (isset($this->cache[$type][$id])) {
            unset($this->cache[$type]);
        }
    }

    /**
     * Reads the cache objects from the cache file
     * @return null
     */
    private function readFile() {
        if ($this->cache !== null) {
            return;
        }

        if (!$this->file->exists()) {
            $this->cache = array();
            return;
        }

        $serializedValue = $this->file->read();
        $this->cache = unserialize($serializedValue);
    }

    /**
     * Writes the cache file with all the cache objects
     * @return null
     */
    private function writeFile() {
        if ($this->cache === null) {
            return;
        }

        $parent = $this->file->getParent();
        $parent->create();

        $output = serialize($this->cache);
        $this->file->write($output);
    }

}