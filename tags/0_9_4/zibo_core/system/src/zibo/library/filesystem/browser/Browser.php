<?php

namespace zibo\library\filesystem\browser;

/**
 * Interface to find files in the Zibo filesystem structure
 */
interface Browser {

    /**
     * Get the root path of this browser
     * @return zibo\library\filesystem\File
     */
    public function getRootPath();

    /**
     * Get the base paths of the Zibo filesystem structure. This will return the path of application, the modules and system.
     * @param boolean $refresh set to true to reread the include paths
     * @return array array with File instances
     */
    public function getIncludePaths($refresh = false);

    /**
     * Get the first file in the Zibo filesystem structure according to the provided path
     * @param string $path relative path of a file in the Zibo filesystem structure
     * @return zibo\library\filesystem\File
     */
    public function getFile($path);

    /**
     * Get all the files in the Zibo filesystem structure according to the provided path
     * @param string $path relative path of a file in the Zibo filesystem structure
     * @return array array with File instances
     */
    public function getFiles($path);

    /**
     * Do the initialization of the browser again to make sure new files are included
     * @return null
     */
    public function reset();

}