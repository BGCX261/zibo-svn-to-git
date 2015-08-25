<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\i18n\I18n;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Table decorator to set a static value to cells
 */
class StaticDecorator implements Decorator {

    /**
     * The value to set to the cells
     * @var string
     */
    private $value;

    /**
     * Constructs a new decorator
     * @param string $value The value to set to the cells
     * @param boolean $translate Set to true to translate the value
     * @return null
     * @throws zibo\ZiboException when an empty value is provided and the translate flag is set to true
     */
    public function __construct($value, $translate = false) {
        if (!$translate) {
            $this->value = $value;
            return;
        }

        if (String::isEmpty($value)) {
            throw new ZiboException('The provided value is empty. When translating the value, a value is required');
        }

        $translator = I18n::getInstance()->getTranslator();

        $this->value = $translator->translate($value);
    }

    /**
     * Decorates the table cell by setting the static value to it
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $cell->setValue($this->value);
    }

}