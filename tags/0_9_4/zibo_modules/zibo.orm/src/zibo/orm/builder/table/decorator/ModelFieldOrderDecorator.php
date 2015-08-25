<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Image;
use zibo\library\orm\definition\field\ModelField;

/**
 * Decorator for the order of the model fields
 */
class ModelFieldOrderDecorator implements Decorator {

    /**
     * Path to the image for the order handle
     * @var string
     */
    const IMAGE_HANDLE = 'web/images/order.png';

    /**
     * Decorates the cell
     * @param zibo\library\html\table\Cell $cell Cell of the value to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $field = $cell->getValue();
        if (!($field instanceof ModelField)) {
            return;
        }

        $row->setId('field_' . $field->getName());
        $cell->appendToClass('action');

        $image = new Image(self::IMAGE_HANDLE);
        $image->appendToClass('handle');

        $cell->setValue($image->getHtml());
    }

}