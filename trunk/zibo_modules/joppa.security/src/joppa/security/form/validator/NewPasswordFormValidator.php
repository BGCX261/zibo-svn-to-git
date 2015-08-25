<?php

namespace joppa\security\form\validator;

use joppa\security\form\NewPasswordForm;

use zibo\library\html\form\FormValidator;
use zibo\library\html\form\Form;
use zibo\library\validation\ValidationError;

/**
 * Validator for the reset password form
 */
class NewPasswordFormValidator implements FormValidator {

	/**
	 * Translation key for the error when no username or password provided
	 * @var string
	 */
	const TRANSLATION_ERROR_PASSWORD_MATCH = 'joppa.security.error.password.match';

    /**
     * Performs validation of the provided form.
     * @param Form $form The form to validate
     * @return null
     */
	public function isValid(Form $form) {
        $password = $form->getPassword();
        $passwordConfirm = $form->getPasswordConfirmation();

        if ($password && $passwordConfirm && $password != $passwordConfirm) {
            $error = new ValidationError(self::TRANSLATION_ERROR_PASSWORD_MATCH, 'The provided passwords do not match');

            $validationException = $form->getValidationException();
            $validationException->addErrors(NewPasswordForm::FIELD_PASSWORD, array($error));
        }
	}

}