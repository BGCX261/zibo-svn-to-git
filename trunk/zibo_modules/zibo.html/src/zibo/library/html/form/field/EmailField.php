<?php

namespace zibo\library\html\form\field;

use zibo\library\validation\validator\EmailValidator;
use zibo\library\String;

/**
 * Email field implementation
 */
class EmailField extends StringField {

    /**
     * Adds a email validator to this field
     * @return null
     */
    protected function init() {
        $this->addValidator(new EmailValidator(array(EmailValidator::OPTION_REQUIRED => 0)));
    }

}