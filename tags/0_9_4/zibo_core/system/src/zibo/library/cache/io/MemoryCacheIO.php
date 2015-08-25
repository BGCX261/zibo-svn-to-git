<?php

namespace zibo\library\cache\io;

use zibo\library\cache\CacheObject;
use zibo\library\filesystem\File;

use \Exception;

/**
 * Memory implementation for the cache input/output. All cache objects are stored in 1 file which is read when the cache is first accessed and written when the cache destructs.
 */
class MemoryCacheIO implements CacheIO {

    const DEFAULT_FILE = 'application/data/cache/memory.cache';

    private $file;
    private $cache;

    public function __construct(File $file = null) {
        if ($file === null) {
            $file = new File(self::DEFAULT_FILE);
        }
        $this->file = $file;
        $this->cache = null;
    }

    public function __destruct() {
        try {
            $this->writeFile();
        } catch (Exception $exception) {
            echo $exception->getMessage() . "<br />\n" . $exception->getTraceAsString();
        }
    }

    public function writeToCache(CacheObject $object) {
        $this->readFile();

        $type = $object->getType();
        $id = $object->getId();

        if (!isset($this->cache[$type])) {
            $this->cache[$type] = array();
        }

        $this->cache[$type][$id] = $object;
    }

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