<?php

namespace zibo\library\html\form\field;

/**
 * Hidden field implementation
 */
class HiddenField extends AbstractField {

    /**
     * Gets the HTML of this hidden field
     * @return string
     */
    public function getHtml() {
        return '<input type="hidden"' .
            $this->getIdHtml() .
            $this->getNameHtml() .
            $this->getDisplayValueHtml() .
            $this->getAttributesHtml() .
            ' />';
    }

}