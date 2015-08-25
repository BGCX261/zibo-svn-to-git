<?php

namespace zibo\database\admin\table\decorator;

use zibo\library\html\form\field\OptionField;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\table\ExtendedTable;

/**
 * Option decorator for a connection, to implement table actions
 *
 */
class OptionDecorator implements Decorator {

    /**
     * Decorates the connection with a form option
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $connection = $cell->getValue();

        $field = new OptionField(ExtendedTable::FIELD_ID, $connection->getName());
        $field->setIsMultiple(true);

        $cell->appendToClass('action');
        $cell->setValue($field->getHtml());
    }

}