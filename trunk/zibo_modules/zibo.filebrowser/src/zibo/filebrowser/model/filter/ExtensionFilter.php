<?php

namespace zibo\filebrowser\model\filter;

use zibo\library\filesystem\File;
use zibo\library\Boolean;

use zibo\ZiboException;

/**
 * Filter for extensions
 */
class ExtensionFilter implements Filter {

    private $extensions;

    /**
     * Flag to see if directories should be included
     * @var boolean
     */
    private $include;

    /**
     * Constructs a new extension filter
     * @param string|array $extensions String or array with extensions
     * @param boolean $include True to allow files with an extension set to this filter, false otherwise
     * @return null
     */
    public function __construct($extensions, $include = true) {
        $this->setExtensions($extensions);
        $this->include = Boolean::getBoolean($include);
    }

    /**
     * Sets the extensions for this filter
     * @param string|array $extensions String or array with extensions
     * @return null
     * @throws zibo\ZiboException if the provided extensions variable is not a string or array
     */
    private function setExtensions($extensions) {
        if (is_string($extensions)) {
            $this->extensions = array($extensions);
            return;
        }

        if (is_array($extensions)) {
            $this->extensions = $extensions;
            return;
        }

        throw new ZiboException('Provided extensions is not a string and not an array');
    }

    /**
     * Checks if the provided file is allowed by this filter
     * @param zibo\library\filesystem\File $file File to check
     * @return boolean True if the file is allowed, false otherwise
     */
    public function isAllowed(File $file) {
        $result = !$this->include;

        $extension = $file->getExtension();
        if (in_array($extension, $this->extensions)) {
            $result = !$result;
        }

        return $result;
    }

}