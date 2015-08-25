<?php

namespace zibo\library\html\form\field;

use zibo\library\validation\validator\WebsiteValidator;
use zibo\library\String;

/**
 * Website field implementation
 */
class WebsiteField extends StringField {

    /**
     * Adds a website validator to this field
     * @return null
     */
    protected function init() {
        $this->addValidator(new WebsiteValidator(array(WebsiteValidator::OPTION_REQUIRED => 0)));
    }

    /**
     * Process the request, get the field value
     * @return null
     */
    public function processRequest() {
        parent::processRequest();

        if ($this->value && !String::looksLikeUrl($this->value)) {
            $this->value = 'http://' . $this->value;
        }
    }

}