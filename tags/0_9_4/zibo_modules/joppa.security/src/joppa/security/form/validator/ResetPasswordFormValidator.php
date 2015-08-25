<?php

namespace joppa\security\form\validator;

use joppa\security\form\ResetPasswordForm;

use zibo\library\html\form\FormValidator;
use zibo\library\html\form\Form;
use zibo\library\validation\ValidationError;

/**
 * Validator for the reset password form
 */
class ResetPasswordFormValidator implements FormValidator {

	/**
	 * Translation key for the error when no username or password provided
	 * @var string
	 */
	const TRANSLATION_ERROR_NO_INPUT = 'joppa.security.error.forgot.password.input';

    /**
     * Performs validation of the provided form.
     * @param Form $form The form to validate
     * @return null
     */
	public function isValid(Form $form) {
        $username = $form->getUsername();
        $email = $form->getEmail();

        if (!$username && !$email) {
            $error = new ValidationError(self::TRANSLATION_ERROR_NO_INPUT, 'Please provide a username or a password');

            $validationException = $form->getValidationException();
            $validationException->addErrors(ResetPasswordForm::FIELD_USERNAME, array($error));
        }
	}

}