<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

/**
 * Interface to decorate a table cell
 */
interface Decorator {

    /**
     * Decorates a table cell by setting a new value to the provided cell object
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null|boolean When used as group decorator, return true to display the group row, false or null otherwise
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues);

}