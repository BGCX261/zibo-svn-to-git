<?php

namespace zibo\library\html\table;

use zibo\library\html\AbstractElement;

/**
 * Table row element
 */
class Row extends AbstractElement {

    /**
     * Array with Cell objects
     * @var array
     */
    private $cells = array();

    /**
     * Adds a cell to this row
     * @param Cell $cell
     * @return null
     */
    public function addCell(Cell $cell) {
        $this->cells[] = $cell;
    }

    /**
     * Checks whether this row has cells
     * @return boolean true when the row has cells, false otherwise
     */
    public function hasCells() {
        return !empty($this->cells);
    }

    /**
     * Gets the cells of this row
     * @return array Array with Cell objects
     */
    public function getCells() {
        return $this->cells;
    }

    /**
     * Gets the HTML of this row
     * @return string
     */
    public function getHtml() {
        $html = '<tr' . $this->getIdHtml() . $this->getClassHtml() . $this->getAttributesHtml() . '>';

        foreach ($this->cells as $cell) {
            $html .= $cell->getHtml();
        }

        $html .= '</tr>';

        return $html;
    }

}