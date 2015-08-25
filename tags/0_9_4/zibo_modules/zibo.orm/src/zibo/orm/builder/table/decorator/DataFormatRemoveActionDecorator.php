<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\orm\definition\DataFormat;

use zibo\orm\builder\wizard\DataFormatStep;
use zibo\orm\builder\wizard\BuilderWizard;

/**
 * Decorator for a data format
 */
class DataFormatRemoveActionDecorator implements Decorator {

    /**
     * URL to the action for the field name
     * @var string
     */
    private $action;

    /**
     * Constructs a new index decorator
     * @param string $action URL to the action for the index name
     * @return null
     */
    public function __construct(BuilderWizard $wizard) {
        $this->wizard = $wizard;
    }

    /**
     * Decorates the cell
     * @param zibo\library\html\table\Cell $cell Cell of the value to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $dataFormat = $cell->getValue();
        if (!($dataFormat instanceof DataFormat)) {
            return;
        }

        $dataFormatName = $dataFormat->getName();

        $button = $this->wizard->getField(DataFormatStep::BUTTON_REMOVE);

        $cell->setValue($button->getOptionHtml($dataFormatName));
        $cell->appendToClass('action');
    }

}