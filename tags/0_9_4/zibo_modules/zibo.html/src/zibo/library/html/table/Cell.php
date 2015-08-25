<?php

namespace zibo\library\html\table;

use zibo\library\html\AbstractElement;

/**
 * Table cell element
 */
class Cell extends AbstractElement {

    /**
     * The value of the cell
     * @var mixed
     */
    protected $value;

    /**
     * Constructs a new cell
     * @param mixed $value Value for the cell
     * @return null
     */
    public function __construct($value = null) {
        $this->setValue($value);
    }

    /**
     * Sets the value for this cell
     * @param mixed $value Value for the cell
     * @return null
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Gets the value of this cell
     * @return mixed Value of the cell
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Gets the HTML for this cell
     * @return string
     */
    public function getHtml() {
        return '<td' .
            $this->getIdHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            '>' .
            $this->value .
            '</td>';
    }

}