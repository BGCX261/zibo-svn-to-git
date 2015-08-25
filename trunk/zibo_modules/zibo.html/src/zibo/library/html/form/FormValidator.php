<?php

namespace zibo\library\html\form;

/**
 * Validator for a form to perform a validation of dependant fields
 */
interface FormValidator {

    /**
     * Performs validation of the provided form. You can use this interface to validate dependant
     * fields. Add validation errors to the validation exception of the form
     * @param Form $form The form to validate
     * @return null
     */
    public function isValid(Form $form);

}
