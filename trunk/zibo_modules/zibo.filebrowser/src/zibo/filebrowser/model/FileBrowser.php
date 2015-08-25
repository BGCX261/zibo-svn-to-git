<?php

namespace zibo\filebrowser\model;

use zibo\filebrowser\model\filter\Filter;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\filesystem\File;

use zibo\ZiboException;

/**
 * Read the filesystem with extra options
 */
class FileBrowser {

    /**
     * Default root path
     * @var string
     */
    const DEFAULT_PATH = '.';

    /**
     * The root path
     * @var zibo\library\filesystem\File
     */
    private $root;

    /**
     * The absolute path of the root
     * @var string
     */
    private $rootAbsolutePath;

    /**
     * Constructs a new file browser
     * @param zibo\library\filesystem\File $root The root of your browser
     * @return null
     */
    public function __construct(File $root) {
        $this->setRoot($root);
    }

    /**
     * Sets the root path of the browser
     * @param zibo\library\filesystem\File $root
     * @return null
     */
    public function setRoot(File $root) {
        $this->root = $root;
        $this->rootAbsolutePath = $root->getAbsolutePath();
    }

    /**
     * Gets the root path of the browser
     * @return zibo\library\filesystem\File
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * Reads from the filesystem with extra options
     * @param zibo\library\filesystem\File $path The path of the directory to read
     * @param array $filters Array with filters
     * @param boolean $recursive Set to true to read recursivly
     * @return array Array with the directories and files of the provided path
     */
    public function readDirectory(File $path = null, array $filters = array(), $recursive = false) {
        $path = new File($this->root, $path);

        if (!$path->exists()) {
            throw new FileSystemException($path->getPath() . ' does not exist');
        }
        if (!$path->isDirectory()) {
            throw new FileSystemException($path->getPath() . ' is not a directory');
        }
        if (!$path->isReadable()) {
            throw new FileSystemException($path->getPath() . ' is not readable');
        }

        $paths = array();

        $files = $path->read($recursive);
        foreach ($files as $file) {
            $path = $this->getPath($file, false);
            $paths[$path->getPath()] = $path;
        }

        if ($filters) {
            $paths = $this->applyFilters($paths , $filters);
        }

        return $paths;
    }

    /**
     * Apply the provided filters on the provided files
     * @param array $files Array with files and directories relative to the root of this browser
     * @param array $filters Array with file browser filters
     * @param array $filters Array with file browser filters
     * @return array Array with the files and directies which passed the provided filters
     */
    public function applyFilters(array $files, array $filters) {
        $this->checkFilters($filters);

        $filteredFiles = array();
        foreach ($files as $key => $file) {
            if (!($file instanceof File)) {
                throw new ZiboException('Element at key ' . $key . ' is not an instance of zibo\\library\\filesystem\File');
            }

            $testFile = new File($this->root, $file);

            $allow = true;
            foreach ($filters as $filter) {
                if (!$filter->isAllowed($testFile)) {
                    $allow = false;
                    break;
                }
            }

            if ($allow) {
                $filteredFiles[$key] = $file;
            }
        }

        return $filteredFiles;
    }

    /**
     * Checks if the provided array of filters are all valid filter objects
     * @param array $filters Array with so called filters
     * @return null
     * @throws zibo\ZiboException when no filters provided
     * @throws zibo\ZiboException when a invalid filter is in the provided array
     */
    private function checkFilters(array $filters) {
        if (!$filters) {
            throw new ZiboException('No filters provided');
        }

        foreach ($filters as $filter) {
            if (!($filter instanceof Filter)) {
                throw new ZiboException('Invalid filter provided, use instance of zibo\\filebrowser\\filter\\Filter');
            }
        }
    }

    /**
     * Checks if the provided path is a directory in the root of this browser
     * @param zibo\library\filesystem\File $path Path to check
     * @return boolean True if the path is a directory in the root of this browser, false otherwise
     */
    public function isDirectory(File $path) {
        $path = new File($this->root, $path);
        return $path->isDirectory();
    }

    /**
     * Gets the size of the provided file
     * @param zibo\library\filesystem\File $path File to get the size of
     * @return integer The size of the file
     */
    public function getSize(File $path) {
        $path = new File($this->root, $path);
        return $path->getSize();
    }

    /**
     * Checks if the provided file is in the provided root path
     * @param zibo\library\filesystem\File $root The root path
     * @param zibo\library\filesystem\File $file The file to check
     * @return boolean True if the file is in the root of the browser, false otherwise
     */
    public function isInRootPath(File $file) {
        $fileAbsolutePath = $file->getAbsolutePath();

        if (strpos($fileAbsolutePath, $this->rootAbsolutePath) === false) {
            return false;
        }

        return true;
    }

    /**
     * Gets the relative path of the provided file to the root path of the browser
     * @param zibo\library\filesystem\File $file File to get the path for
     * @param boolean $addRoot Set to false if the root of the browser is already added to the provided file
     * @return zibo\library\filesystem\File The relative path of the provided file
     */
    public function getPath(File $file = null, $addRoot = true) {
        if (!$file) {
            return new File(self::DEFAULT_PATH);
        }

        if ($addRoot) {
            $file = new File($this->root, $file);
        }
        $fileAbsolutePath = $file->getAbsolutePath();

        $file = str_replace($this->rootAbsolutePath, '', $fileAbsolutePath);
        if (!$file) {
            $file = self::DEFAULT_PATH;
        } else {
            if ($file[0] === File::DIRECTORY_SEPARATOR) {
                $file = substr($file, 1);
            }
        }

        return new File($file);
    }

    /**
     * Orders the provided files ascending by name
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered ascending by name
     */
    public function orderByNameAscending(array $files) {
        usort($files, array($this, 'compareByNameAscending'));
        return $files;
    }

    /**
     * Orders the provided files descending by name
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered descending by name
     */
    public function orderByNameDescending(array $files) {
        usort($files, array($this, 'compareByNameDescending'));
        return $files;
    }

    /**
     * Orders the provided files ascending by extension
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered ascending by extension
     */
    public function orderByExtensionAscending(array $files) {
        usort($files, array($this, 'compareByExtensionAscending'));
        return $files;
    }

    /**
     * Orders the provided files descending by extension
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered extension by name
     */
    public function orderByExtensionDescending(array $files) {
        usort($files, array($this, 'compareByExtensionDescending'));
        return $files;
    }

    /**
     * Orders the provided files ascending by size
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered ascending by size
     */
    public function orderBySizeAscending(array $files) {
        usort($files, array($this, 'compareBySizeAscending'));
        return $files;
    }

    /**
     * Orders the provided files descending by size
     * @param array $files Array with files and directories to order
     * @return array The provided files and directories ordered descending by size
     */
    public function orderBySizeDescending(array $files) {
        usort($files, array($this, 'compareBySizeDescending'));
        return $files;
    }

    private function compareByNameDescending($a, $b) {
        return !$this->compareByNameAscending($a, $b);
    }

    private function compareByNameAscending($a, $b) {
        return strcmp($a->getName(), $b->getName());
    }

    private function compareByExtensionDescending($a, $b) {
        return !$this->compareByExtensionAscending($a, $b);
    }

    private function compareByExtensionAscending($a, $b) {
        $a = $a->getExtension() . '---' . $a->getName();
        $b = $b->getExtension() . '---' . $b->getName();
        return strcmp($a, $b);
    }

    private function compareBySizeDescending($a, $b) {
        return !$this->compareBySizeAscending($a, $b);
    }

    private function compareBySizeAscending($a, $b) {
        if (!$a->exists() || !$b->exists() || ($a->isDirectory() || $b->isDirectory())) {
            return $this->compareByNameAscending($a, $b);
        }

        $a = $a->getSize();
        $b = $b->getSize();

        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

}