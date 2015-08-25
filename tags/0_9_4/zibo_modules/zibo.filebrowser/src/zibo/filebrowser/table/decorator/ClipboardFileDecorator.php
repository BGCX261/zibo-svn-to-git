<?php

namespace zibo\filebrowser\table\decorator;

use zibo\library\filesystem\File;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

/**
 * Decorator for a file in the clipboard
 */
class ClipboardFileDecorator extends  AbstractFileDecorator {

    /**
     * The root of the file browser
     * @var zibo\library\filesystem\File
     */
    private $root;

    /**
     * Constructs a new clipboard file decorator
     * @param zibo\library\filesystem\File $root The root of the file browser
     * @return null
     */
    public function __construct(File $root) {
        $this->root = $root;
    }

    /**
     * Decorates the cell with the path of the file
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $file = $cell->getValue();

        $absoluteFile = new File($this->root, $file);

        if (!$absoluteFile->exists()) {
            $cell->setValue('---');
            return;
        }

        if ($absoluteFile->isDirectory()) {
            $class = self::CLASS_DIRECTORY;
        } else {
            $class = self::CLASS_FILE;
        }

        $html = $this->getNameHtml($file->getPath(), $class);

        $cell->setValue($html);
    }

}