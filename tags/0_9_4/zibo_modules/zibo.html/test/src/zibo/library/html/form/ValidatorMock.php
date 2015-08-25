<?php

namespace zibo\library\html\form;

use zibo\library\validation\validator\Validator;

class ValidatorMock implements Validator {

    public function __construct(array $options = null) {

    }

    public function isValid($value) {

    }

    public function getErrors() {

    }

}