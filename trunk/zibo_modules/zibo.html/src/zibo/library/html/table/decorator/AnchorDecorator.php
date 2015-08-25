<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

/**
 * Abstract decorator to create an anchor from a cell value
 */
abstract class AnchorDecorator implements Decorator {

    /**
     * Base href attribute for the anchor
     * @var string
     */
    protected $href;

    /**
     * Constructs a new anchor decorator
     * @param string $href Base href attribute for the anchor
     */
    public function __construct($href) {
        $this->href = $href;
    }

    /**
     * Decorates a table cell by setting an anchor to the cell based on the cell's value
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $value = $cell->getValue();

        $label = $this->getLabelFromValue($value);
        $href = $this->getHrefFromValue($value);

        $anchor = new Anchor($label, $href);

        $this->processAnchor($anchor, $value);

        $cell->setValue($anchor->getHtml());
    }

    /**
     * Gets the label for the anchor
     * @param mixed $value Value of the cell
     * @return string Label for the anchor
     */
    abstract protected function getLabelFromValue($value);

    /**
     * Gets the href attribute for the anchor
     * @param mixed $value Value of the cell
     * @return string Href attribute for the anchor
     */
    abstract protected function getHrefFromValue($value);

    /**
     * Hook to perform extra processing on the generated anchor
     * @param zibo\library\html\Anchor $anchor Generated anchor for the cell
     * @param mixed $value Value of the cell
     * @return null
     */
    protected function processAnchor(Anchor $anchor, $value) {

    }

}