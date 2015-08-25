<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\orm\definition\DataFormat;

/**
 * Decorator for a data format
 */
class DataFormatDecorator implements Decorator {

    /**
     * URL to the action for the data format name
     * @var string
     */
    private $action;

    /**
     * Constructs a new data format decorator
     * @param string $action URL to the action for the data format name
     * @return null
     */
    public function __construct($action = null) {
        $this->action = $action;
    }

    /**
     * Decorates the cell of a data format
     * @param zibo\library\html\table\Cell $cell Cell containing the value to decorate
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

        if ($this->action) {
            $anchor = new Anchor($dataFormatName, $this->action . $dataFormatName);
            $value = $anchor->getHtml();
        } else {
            $value = $dataFormatName;
        }

        $value .= '<div class="info">';
        $value .= $dataFormat->getFormat();
        $value .= '</div>';

        $cell->setValue($value);
    }

}