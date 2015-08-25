<?php

namespace zibo\library\filesystem\browser;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * Abstract browser to find files in the Zibo filesystem structure
 */
abstract class AbstractBrowser implements Browser {

    /**
     * root path of this browser
     * @var zibo\library\filesystem\File
     */
    protected $rootPath;

    /**
     * array containing the base paths of the Zibo filesystem structure
     * @var array
     */
    protected $includePaths;

    /**
     * Construct the browser
     * @param zibo\library\filesystem\File $rootPath root path of this browser
     * @return null
     */
    public function __construct(File $rootPath) {
        $this->rootPath = $rootPath;
        $this->initialize();
    }

    /**
     * Hook in the constructor
     * @return null
     */
    protected function initialize() {

    }

    /**
     * Get the root path of this browser
     * @return zibo\library\filesystem\File
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * Get the base paths of the Zibo filesystem structure. This will return the path of application, the modules and system.
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
     * Get the first file in the Zibo filesystem structure according to the provided path
     * @param string $fileName relative path of a file in the Zibo filesystem structure
     * @return zibo\library\filesystem\File
     */
    public function getFile($fileName) {
        return $this->lookupFile($fileName, true);
    }

    /**
     * Get all the files in the Zibo filesystem structure according to the provided path
     * @param string $fileName relative path of a file in the Zibo filesystem structure
     * @return array array with File instances
     */
    public function getFiles($fileName) {
        return $this->lookupFile($fileName, false);
    }

    /**
     * Look for files
     * @param string $fileName relative path of a file in the Zibo filesystem structure
     * @param boolean $firstOnly true to get the first matched file, false to get an array
     *                           with all the matched files
     * @return zibo\library\filesystem\File|array Depending on the firstOnly flag, an instance or an array of zibo\library\filesystem\File
     * @throws zibo\ZiboException when fileName is empty or not a string
     */
    abstract protected function lookupFile($fileName, $firstOnly);

    /**
     * Resets the browser by rereading the include paths
     * @return null
     */
    public function reset() {
        $this->getIncludePaths(true);
    }

}