<?php

namespace zibo\library\html\table;

use zibo\library\html\AbstractElement;
use zibo\library\html\table\decorator\Decorator;

/**
 * Table HTML element
 */
class Table extends AbstractElement {

    /**
     * Header row of the table
     * @var Row
     */
    protected $header;

    /**
     * Array with Row objects
     * @var array
     */
    protected $rows = array();

    /**
     * Sets a header row for this table
     * @param Row $row
     * @return null
     */
    public function setHeader(Row $row) {
        $this->header = $row;
    }

    /**
     * Adds a data row to this table
     * @param Row $row
     */
    public function addRow(Row $row) {
        $this->rows[] = $row;
    }

    /**
     * Checks whether this table has rows
     * @return boolean true if the table has rows, false otherwise
     */
    public function hasRows() {
        return !empty($this->rows);
    }

    /**
     * Gets the number of rows set to this table
     * @return integer Number of rows
     */
    public function countRows() {
        return count($this->rows);
    }

    /**
     * Gets the HTML of this table
     * @return string
     */
    public function getHtml() {
        $html = '<table' . $this->getIdHtml() . $this->getClassHtml() . $this->getAttributesHtml() . '>' . "\n";

        if ($this->header) {
            $html .= "\t" . $this->header->getHtml() . "\n";
        }

        foreach ($this->rows as $row) {
            $html .= "\t" . $row->getHtml() . "\n";
        }

        $html .= '</table>' . "\n";

        return $html;
    }

}