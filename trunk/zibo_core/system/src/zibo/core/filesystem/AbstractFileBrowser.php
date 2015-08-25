<?php

namespace zibo\core\filesystem;

use zibo\core\Zibo;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\filesystem\File;

/**
 * Abstract file browser to find files in the Zibo filesystem structure
 */
abstract class AbstractFileBrowser implements FileBrowser {

    /**
     * Root path for this file browser
     * @var zibo\library\filesystem\File
     */
    protected $rootPath;

    /**
     * Array containing the base paths of the Zibo filesystem structure
     * @var array
     */
    protected $includePaths;

    /**
     * Constructs a new file browser
     * @param zibo\library\filesystem\File $rootPath Root path for this browser
     * @return null
     */
    public function __construct(File $rootPath) {
        $this->setRootPath($rootPath);
        $this->initialize();
    }

    /**
     * Hook in the constructor
     * @return null
     */
    protected function initialize() {

    }

    /**
     * Sets the root path
     * @param File $rootPath The root path
     * @throws FileSystemException
     */
    private function setRootPath(File $rootPath) {
        if (!$rootPath->isDirectory()) {
            throw new FileSystemException('Could not build the index: ' . $this->rootPath->getPath() . ' is not a directory.');
        }

        $this->rootPath = $rootPath;
    }

    /**
     * Gets the root path
     * @return zibo\library\filesystem\File
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * Gets the base paths of the Zibo filesystem structure. This will return
     * the path of application, the modules and system.
     * @param boolean $refresh set to true to reread the include paths
     * @return array array with File instances
     */
    public function getIncludePaths($refresh = false) {
        if ($this->includePaths && !$refresh) {
            return $this->includePaths;
        }

        $this->includePaths = array();
        $this->includePaths[] = new File($this->rootPath, Zibo::DIRECTORY_APPLICATION);

        $modulePath = new File($this->rootPath, Zibo::DIRECTORY_MODULES);

        $moduleFiles = $modulePath->read();
        foreach ($moduleFiles as $moduleFile) {
            if (!$moduleFile->exists() || !($moduleFile->isPhar() || $moduleFile->isDirectory())) {
                continue;
            }

            $this->includePaths[] = $moduleFile;
        }

        $this->includePaths[] = new File($this->rootPath, Zibo::DIRECTORY_SYSTEM);

        return $this->includePaths;
    }

    /**
     * Gets the relative file in the Zibo file structure for a given
     * absolute file.
     * @param string|zibo\library\filesystem\File $file Path to a file to get
     * the relative file from
     * @return zibo\library\filesystem\File relative file in the Zibo file
     * structure if located in the root of the Zibo installation
     * @throws zibo\ZiboException when the provided file is not in the root path
     * @throws zibo\ZiboException when the provided file is not part of the Zibo
     * file system structure
     */
    public function getRelativeFile($file) {
        $file = new File($file);
        $absoluteFile = $file->getAbsolutePath();

        $rootPath = $this->getRootPath();

        $isPhar = $file->hasPharProtocol();

        $file = str_replace($rootPath->getPath() . File::DIRECTORY_SEPARATOR, '', $absoluteFile);
        if ($file == $absoluteFile) {
            throw new FileSystemException($file . ' is not in the root path');
        }

        if ($isPhar) {
            $file = substr($file, 7);
        }

        $tokens = explode(File::DIRECTORY_SEPARATOR, $file);
        $token = array_shift($tokens);

        if ($token == Zibo::DIRECTORY_APPLICATION || $token == Zibo::DIRECTORY_SYSTEM) {
            $token = array_pop($tokens);
            return new File(implode(File::DIRECTORY_SEPARATOR, $tokens), $token);
        }

        if ($token !== Zibo::DIRECTORY_MODULES || count($tokens) < 2) {
            throw new FileSystemException($file . ' is not in the Zibo file system structure (' . $token . ')');
        }

        array_shift($tokens);
        $token = array_pop($tokens);

        return new File(implode(File::DIRECTORY_SEPARATOR, $tokens), $token);
    }

    /**
     * Gets the first file in the Zibo filesystem structure according to the
     * provided path.
     * @param string $file Relative path of a file in the Zibo filesystem
     * structure
     * @return zibo\library\filesystem\File|null Instance of the file if found,
     * null otherwise
     */
    public function getFile($file) {
        return $this->lookupFile($file, true);
    }

    /**
     * Gets all the files in the Zibo filesystem structure according to the
     * provided path.
     * @param string $file Relative path of a file in the Zibo filesystem
     * structure
     * @return array array with File instances
     * @see zibo\library\filesystem\File
     */
    public function getFiles($file) {
        return $this->lookupFile($file, false);
    }

    /**
     * Look for files
     * @param string $file Relative path of a file in the Zibo filesystem
     * structure
     * @param boolean $firstOnly true to get the first matched file, false
     * to get an array with all the matched files
     * @return zibo\library\filesystem\File|array Depending on the firstOnly
     * flag, an instance or an array of File
     * @throws zibo\ZiboException when file is empty or not a string
     */
    abstract protected function lookupFile($file, $firstOnly);

    /**
     * Resets the browser
     * @return null
     */
    public function reset() {
        $this->includePaths = null;
    }

}