<?php

namespace zibo\filebrowser\table\decorator;

use zibo\filebrowser\model\FileBrowser;

use zibo\library\filesystem\File;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

/**
 * Decorator to create a edit action of a File
 */
class EditActionDecorator extends FileActionDecorator {

    /**
     * Translation key for the label of the button
     * @var string
     */
    const TRANSLATION_EDIT = 'button.edit';

    /**
     * Extensions which are allowed to be edited
     * @var array
     */
    private $extensions;

    /**
     * Constructs a new edit action decorator
     * @param string $action The URL to the edit action
     * @param zibo\filebrowser\model\FileBrowser $fileBrowser The file browser
     * @param array $extensions Array with extensions which are allowed to be edited
     * @return null
     */
    public function __construct($action, FileBrowser $fileBrowser, array $extensions = array('txt' => 'txt')) {
        parent::__construct($action, self::TRANSLATION_EDIT);

        $this->fileBrowser = $fileBrowser;
        $this->extensions = $extensions;
    }

    /**
     * Decorates a table cell by setting an anchor to the cell based on the cell's value
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $cell->appendToClass(self::CLASS_ACTION);

        $file = $cell->getValue();
        if ($this->fileBrowser->isDirectory($file)) {
            $cell->setValue('');
            return;
        }

        $extension = $file->getExtension();
        if (array_key_exists($extension, $this->extensions)) {
            parent::decorate($cell, $row, $rowNumber, $remainingValues);
        } else {
            $cell->setValue('');
        }
    }

}