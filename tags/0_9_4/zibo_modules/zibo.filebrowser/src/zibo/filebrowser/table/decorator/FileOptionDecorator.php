<?php

namespace zibo\filebrowser\table\decorator;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\table\ExtendedTable;

/**
 * Decorator to create an option field of a File
 */
class FileOptionDecorator implements Decorator {

    /**
     * Decorates the cell with the option for the File
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $file = $cell->getValue();

        if (!($file instanceof File)) {
            return;
        }

        $fieldFactory = FieldFactory::getInstance();

        $field = $fieldFactory->createField(FieldFactory::TYPE_OPTION, ExtendedTable::FIELD_ID, $file->getPath());
        $field->setIsMultiple(true);

        $cell->appendToClass(FileActionDecorator::CLASS_ACTION);
        $cell->setValue($field->getHtml());
    }

}