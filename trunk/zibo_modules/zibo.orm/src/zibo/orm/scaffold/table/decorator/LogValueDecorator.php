<?php

namespace zibo\orm\scaffold\table\decorator;

use zibo\library\html\table\decorator\ValueDecorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

/**
 * Decorator for the value of a log
 */
class LogValueDecorator extends ValueDecorator {

    /**
     * Class name for the cell to decorate
     * @var string
     */
    private $className;

    /**
     * Constructs a new log value decorator
     * @param string $fieldName The name of the field to decorate
     * @param string $className The class name for the cell to decorate
     * @return null
     */
    public function __construct($fieldName, $className = null) {
        parent::__construct($fieldName);

        if (!$className) {
            $className = $fieldName;
        }

        $this->className = $className;
    }

    /**
     * Decorates the cell
     * @param zibo\library\html\table\Cell $cell Cell of the value to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $number, array $remainingValues) {
        parent::decorate($cell, $row, $number, $remainingValues);

        $cell->appendToClass($this->className);
    }

}