<?php

namespace zibo\library\cache\io;

use zibo\library\cache\CacheObject;
use zibo\library\filesystem\File;

use \Exception;

/**
 * Type memory implementation for the cache input/output. All cache objects of a type are stored in 1 file. This means 1 file for each cache type which is read when accessed.
 */
class TypeMemoryCacheIO implements CacheIO {

    /**
     * Default path for the type cache files
     * @var string
     */
    const DEFAULT_PATH = 'application/data/cache/memory';

    /**
     * Path for the type cache files
     * @var zibo\library\filesystem\File
     */
    private $path;

    /**
     * Array for the loaded cache objects
     * @var array
     */
    private $cache;

    /**
     * Constructs a new cache IO
     * @param zibo\library\filesystem\File $path Path for the type cache files
     * @return null
     */
    public function __construct(File $path = null) {
        if ($path === null) {
            $path = new File(self::DEFAULT_PATH);
        }

        $this->path->create();

        $this->cache = array();
    }

    /**
     * Destructs the cache IO. Writes the loaded cache types to files
     * @return null
     */
    public function __destruct() {
        try {
            $this->writeTypes();
        } catch (Exception $e) {
            echo $e->getMessage() . "<br />\n" . $e->getTraceAsString();
        }
    }

    /**
     * Writes the cache object to the cache
     * @param zibo\library\cache\CacheObject $object
     * @return null
     */
    public function writeToCache(CacheObject $object) {
        $type = $object->getType();
        $id = $object->getId();

        $this->readType($type);

        if (!isset($this->cache[$type])) {
            $this->cache[$type] = array();
        }

        $this->cache[$type][$id] = $object;
    }

    /**
     * Reads cache objects from the cache
     * @param string $type Type of the cache object
     * @param string $id Id of the cache object
     * @return null|array|zibo\library\cache\CacheObject
     */
    public function readFromCache($type, $id = null) {
        $this->readType($type);

        if ($id == null) {
            if (!isset($this->cache[$type])) {
                return array();
            }

            return $this->cache[$type];
        }

        if (!isset($this->cache[$type][$id])) {
            return null;
        }

        return $this->cache[$type][$id];
    }

    /**
     * Clears objects from the cache
     * @param string $type Type of the objects to clear, provide null to clear the complete cache
     * @param string $id Id of the cache object to clear
     * @return null
     */
    public function clearCache($type = null, $id = null) {
        $this->readTypes();

        if (is_null($type)) {
            foreach ($this->cache as $type => $cache) {
                $this->cache[$type] = array();
            }

            return;
        }

        if (is_null($id)) {
            if (isset($this->cache[$type])) {
                $this->cache[$type] = array();
            }

            return;
        }

        if (isset($this->cache[$type][$id])) {
            unset($this->cache[$type][$id]);
        }
    }

    /**
     * Loads a cache type to memory
     * @param string $type Type of the cache object
     * @return null
     */
    private function readType($type) {
        if (isset($this->cache[$type])) {
            return;
        }

        $file = $this->getTypeFile($type);

        if (!$file->exists()) {
            $this->cache[$type] = array();
            return;
        }

        try {
            $serializedValue = $file->read();

            $this->cache[$type] = unserialize($serializedValue);

            if (!is_array($this->cache[$type])) {
                throw new Exception('Could not unserialize the cached value of ' . $file->getPath());
            }
        } catch (Exception $exception) {
            $this->cache[$type] = array();
        }
    }

    /**
     * Loads all the cache types to memory
     * @return null
     */
    private function readTypes() {
        $files = $this->path->read();

        foreach ($files as $file) {
            if ($file->isDirectory()) {
                continue;
            }

            $this->readType($file->getName());
        }
    }

    /**
     * Writes the loaded cache objects to the type files
     * @return null
     */
    protected function writeTypes() {
        foreach ($this->cache as $type => $cache) {
            $file = $this->getTypeFile($type);

            if (empty($cache)) {
                if ($file->exists()) {
                    $file->delete();
                }

                continue;
            }

            $output = serialize($cache);

            $file->write($output);
        }
    }

    /**
     * Gets the cache file for a type
     * @param string $type Type of a cache object
     * @return zibo\library\filesystem\File
     */
    private function getTypeFile($type) {
        return new File($this->path, $type);
    }

}