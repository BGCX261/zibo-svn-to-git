<?php

namespace zibo\core\view;

use zibo\library\filesystem\File;

use zibo\ZiboException;

/**
 * View to render the contents of a file
 */
class FileView implements View {

    /**
     * The file to render
     * @var zibo\library\filesystem\File
     */
    private $file;

    /**
     * Constructs a new file view
     * @param zibo\library\filesystem\File $file File to render
     * @return null
     */
    public function __construct(File $file) {
        if (!$file->exists() || $file->isDirectory()) {
            throw new ZiboException($file . ' does not exists or is a directory.');
        }

        $this->file = $file;
    }

    /**
     * Renders the file view
     * @param boolean $return True to return the contents of the file, false
     * to passthru the file to the output
     * @return null|string
     */
    public function render($return = true) {
        if ($return) {
            return $this->file->read();
        }

        $this->file->passthru();
    }

}