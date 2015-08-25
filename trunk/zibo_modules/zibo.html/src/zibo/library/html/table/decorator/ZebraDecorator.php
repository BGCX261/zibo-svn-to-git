<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Wrapper around a table decorator to decorate the rows with a zebra effect
 */
class ZebraDecorator implements Decorator {

    /**
     * Default style class for a odd row
     * @var string
     */
    const STYLE_ODD = 'odd';

    /**
     * Default style class for a even row
     * @var string
     */
    const STYLE_EVEN = 'even';

    /**
     * Decorator to wrap around
     * @var Decorator
     */
    private $decorator;

    /**
     * Style class for a odd row
     * @var string
     */
    private $styleOdd;

    /**
     * Style class for a even row
     * @var string
     */
    private $styleEven;

    /**
     * Constructs a new zebra decorator
     * @param Decorator $decorator Decorator to wrap around
     * @param string $styleOdd Style class for an odd row (optional)
     * @param string $styleEven Style class for an even row (optional)
     * @return null
     */
    public function __construct(Decorator $decorator, $styleOdd = null, $styleEven = null) {
        $this->decorator = $decorator;
        $this->setStyleOdd($styleOdd);
        $this->setStyleEven($styleEven);
    }

    /**
     * Decorates the table row by adding the odd or even style class depending on the row number
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $this->decorator->decorate($cell, $row, $rowNumber, $remainingValues);

        if ($rowNumber % 2) {
            $row->appendToClass($this->styleOdd);
        } else {
            $row->appendToClass($this->styleEven);
        }
    }

    /**
     * Sets the style class for odd rows
     * @param string $styleOdd
     * @return null
     * @throws zibo\ZiboException when the provided style is not a string
     */
    private function setStyleOdd($styleOdd) {
        if (!$styleOdd) {
            $styleOdd = self::STYLE_ODD;
        }

        String::isEmpty($styleOdd);

        $this->styleOdd = $styleOdd;
    }

    /**
     * Sets the style class for even rows
     * @param string $styleEven
     * @return null
     * @throws zibo\ZiboException when the provided style is not a string
     */
    private function setStyleEven($styleEven) {
        if (!$styleEven) {
            $styleEven = self::STYLE_EVEN;
        }

        String::isEmpty($styleEven);

        $this->styleEven = $styleEven;
    }

}