<?php

namespace zibo\core\filesystem;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\filesystem\File;

/**
 * Indexed browser to find files in the Zibo filesystem structure
 */
class IndexedFileBrowser extends AbstractFileBrowser {

    /**
     * path of the file where the index will be cached
     * @var string
     */
    const CACHE_FILE = 'application/data/cache/filesystem.index';

    /**
     * index of the filesystem with as key the relative filename and as value an array
     * with path's where this file can be found
     * @var array
     */
    protected $index;

    /**
     * array with filenames which are ignored while indexing
     * @var array
     */
    protected $exclude = array(
		'.svn' => null,
		'data/cache' => null,
		'data/session' => null,
		'public' => null,
    );

    /**
     * Load the index from cache or create a new index when the cache doesn't exist
     * @return null
     */
    protected function initialize() {
        $cacheFile = $this->getCacheFile();

        if ($cacheFile->isLocked()) {
            $cacheFile->waitForUnlock();
        }

        if (!$cacheFile->exists()) {
            $this->reset();
            return;
        }

        $serializedIndex = $cacheFile->read();
        $this->index = unserialize($serializedIndex);
    }

    /**
     * Look for files in the index
     * @param string $fileName relative path of a file in the Zibo filesystem structure
     * @param boolean $firstOnly true to get the first matched file, false to get an array
     *                           with all the matched files
     * @return zibo\library\filesystem\File|array Depending on the firstOnly flag, an instance or an array of zibo\library\filesystem\File
     * @throws zibo\ZiboException when fileName is empty or not a string
     */
    protected function lookupFile($fileName, $firstOnly) {
        if ($fileName instanceof File) {
            $fileName = $fileName->getPath();
        }

        if (!isset($this->index[$fileName])) {
            if ($firstOnly) {
                return null;
            }

            return array();
        }

        if ($firstOnly) {
            reset($this->index[$fileName]);
            $file = each($this->index[$fileName]);
            return new File($this->rootPath, $file['value']);
        }

        $files = array();
        foreach ($this->index[$fileName] as $file) {
            $files[] = new File($this->rootPath, $file);
        }

        return $files;
    }

    /**
     * Gets the index of this browser
     * @return array Array with the relative path of a file as key and an array with the full paths of all matching
     * 				 files throughout the application, modules and system as value
     */
    public function getIndex() {
    	return $this->index;
    }

    /**
     * Sets the excluded directories for this file browser
     * @param array $exclude Array with the relative paths of the excluded directories
     * @return null
     */
    public function setExclude(array $exclude) {
        $this->exclude = array();

        foreach ($exclude as $name) {
            $this->exclude[$name] = null;
        }
    }

    /**
     * Gets the excluded directories of this file browser
     * @return array
     */
    public function getExclude() {
    	return array_keys($this->exclude);
    }

    /**
     * Reset the browser by creating a new index of the files according to the
     * Zibo filesystem structure
     * @return null
     */
    public function reset() {
        $cacheFile = $this->getCacheFile();
        if ($cacheFile->isLocked()) {
            $this->initialize();
            return;
        }

        $cacheFile->lock();

        parent::reset();

        $this->includePaths = $this->getIncludePaths();
        $this->index = array();

        foreach ($this->includePaths as $includePath) {
            $this->indexDirectory($includePath);
        }

        foreach ($this->index as $key => $value) {
            sort($this->index[$key]);
        }

        $cacheParent = $cacheFile->getParent();
        $cacheParent->create();

        $cacheFile->write(serialize($this->index));
        $cacheFile->unlock();
    }

    /**
     * Add the files of a directory recursively to the index
     * @param zibo\library\filesystem\File $path
     * @param string $prefix
     * @return null
     */
    private function indexDirectory(File $path, $prefix = null) {
        $files = $path->read();

        foreach ($files as $file) {
            $name = $file->getName();
            if (isset($this->exclude[$name])) {
                continue;
            }

            $name = $prefix . $name;
            if (isset($this->exclude[$name])) {
                continue;
            }

            if ($file->isDirectory()) {
                $this->indexDirectory($file, $name . File::DIRECTORY_SEPARATOR);
            } else {
                $this->indexFile($file, $name);
            }
        }
    }

    /**
     * Add a file to the index
     * @param zibo\library\filesystem\File $path
     * @param string $name
     * @return null
     */
    private function indexFile(File $path, $name) {
        if (!isset($this->index[$name])) {
            $this->index[$name] = array();
        }

        $rootPath = $this->rootPath->getPath();
        $path = $path->getPath();

        $path = str_replace($rootPath . File::DIRECTORY_SEPARATOR, '', $path);

        $this->index[$name][] = $path;

        sort($this->index[$name]);
    }

    /**
     * Get the file where the index will be cached
     * @return zibo\library\filesystem\File
     */
    private function getCacheFile() {
        return new File($this->rootPath, self::CACHE_FILE);
    }

}