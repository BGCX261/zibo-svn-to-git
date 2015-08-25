<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\form\field\OptionField;
use zibo\library\html\table\decorator\ActionDecorator;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\model\Model;

/**
 * Decorator to create an option field for model, needed for the table actions
 */
class ModelOptionDecorator implements Decorator {

    /**
     * Decorates the cell with an option field for the table actions
     * @param zibo\library\html\table\Cell $cell Cell which holds the data object
     * @param zibo\library\html\table\Row $row Row of the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $model = $cell->getValue();

        if (!($model instanceof Model)) {
            $cell->setValue('');
            return;
        }

        $field = new OptionField(ExtendedTable::FIELD_ID, $model->getName());
        $field->setIsMultiple(true);

        $cell->appendToClass(ActionDecorator::STYLE_ACTION);

        $cell->setValue($field->getHtml());
    }

}