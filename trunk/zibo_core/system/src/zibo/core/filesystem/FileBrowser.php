<?php

namespace zibo\core\filesystem;

/**
 * Interface to find files in the Zibo filesystem structure
 */
interface FileBrowser {

    /**
     * Gets the root path
     * @return zibo\library\filesystem\File
     */
    public function getRootPath();

    /**
     * Gets the base paths of the Zibo filesystem structure. This will return
     * the path of application, the modules and system.
     * @param boolean $refresh set to true to reread the include paths
     * @return array array with File instances
     */
    public function getIncludePaths($refresh = false);

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
    public function getRelativeFile($file);

    /**
     * Gets the first file in the Zibo filesystem structure according to the
     * provided path.
     * @param string $file Relative path of a file in the Zibo filesystem
     * structure
     * @return zibo\library\filesystem\File|null Instance of the file if found,
     * null otherwise
     */
    public function getFile($file);

    /**
     * Gets all the files in the Zibo filesystem structure according to the
     * provided path.
     * @param string $file Relative path of a file in the Zibo filesystem
     * structure
     * @return array array with File instances
     * @see zibo\library\filesystem\File
     */
    public function getFiles($file);

    /**
     * Do the initialization of the browser again to make sure new files are
     * included.
     * @return null
     */
    public function reset();

}