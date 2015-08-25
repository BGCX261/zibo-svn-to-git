<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\form\field\OptionField;
use zibo\library\html\table\decorator\ActionDecorator;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\definition\field\ModelField;

/**
 * Decorator to create an option field for a model field, needed for the table actions
 */
class ModelFieldOptionDecorator implements Decorator {

    /**
     * Decorates the cell with an option field for the table actions
     * @param zibo\library\html\table\Cell $cell Cell which holds the data object
     * @param zibo\library\html\table\Row $row Row of the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $modelField = $cell->getValue();

        if (!($modelField instanceof ModelField)) {
            $cell->setValue('');
            return;
        }

        $field = new OptionField(ExtendedTable::FIELD_ID, $modelField->getName());
        $field->setIsMultiple(true);

        $cell->appendToClass(ActionDecorator::STYLE_ACTION);

        $cell->setValue($field->getHtml());
    }

}