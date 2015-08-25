<?php

namespace zibo\library\html\table;

/**
 * Table header cell element
 */
class HeaderCell extends Cell {

    /**
     * Gets the HTML of this element
     * @return string
     */
    public function getHtml() {
        return '<th' .
            $this->getIdHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            '>' .
            $this->value .
            '</th>';
    }

}