<?php

namespace zibo\orm\scaffold\table\decorator;

use zibo\library\html\form\field\OptionField;
use zibo\library\html\table\decorator\ActionDecorator;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\table\ExtendedTable;

/**
 * Decorator to create an option field for a data object, needed for the table actions
 */
class DataOptionDecorator implements Decorator {

    /**
     * Decorates the cell with an option field for the table actions
     * @param zibo\library\html\table\Cell $cell Cell which holds the data object
     * @param zibo\library\html\table\Row $row Row of the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array with the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $values) {
        $data = $cell->getValue();

        if (!is_object($data)) {
            $cell->setValue('');
            return;
        }

        $field = new OptionField(ExtendedTable::FIELD_ID, $data->id);
        $field->setIsMultiple(true);

        $cell->appendToClass(ActionDecorator::STYLE_ACTION);

        $cell->setValue($field->getHtml());
    }

}