<?php

namespace zibo\library\html\form\field;

/**
 * Password field implementation
 */
class PasswordField extends AbstractField {

    /**
     * Gets the HTML of this password field
     * @return string
     */
    public function getHtml() {
        return '<input type="password"' .
            $this->getIdHtml() .
            $this->getNameHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() .
            ' />';
    }

}