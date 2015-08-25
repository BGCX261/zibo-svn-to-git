<?php

namespace zibo\library\html\form\field;

/**
 * Text field implementation
 */
class TextField extends AbstractField {

    /**
     * Gets the HTML of this text field
     * @return string
     */
    public function getHtml() {
        return '<textarea' .
            $this->getIdHtml() .
            $this->getNameHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() .
            '>' .
            htmlspecialchars($this->getDisplayValue(), ENT_NOQUOTES) .
            '</textarea>';
    }

}