<?php

namespace zibo\filebrowser\model\filter;

use zibo\library\filesystem\File;

/**
 * Filter for directories
 */
class DirectoryFilter implements Filter {

    /**
     * Flag to see if directories should be included
     * @var boolean
     */
    private $include;

    /**
     * Construct a new directory filter
     * @param boolean $include True to allow directories, false otherwise
     * @return null
     */
    public function __construct($include = true) {
        $this->include = $include;
    }

    /**
     * Checks if the provided file is allowed by this filter
     * @param zibo\library\filesystem\File $file File to check
     * @return boolean True if the file is allowed, false otherwise
     */
    public function isAllowed(File $file) {
        $result = !$this->include;

        if ($file->isDirectory()) {
            $result = !$result;
        }

        return $result;
    }

}