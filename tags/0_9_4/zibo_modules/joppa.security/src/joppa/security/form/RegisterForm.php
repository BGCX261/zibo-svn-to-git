<?php

namespace joppa\security\form;

use joppa\security\form\validator\NewPasswordFormValidator;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to register a new user
 */
class RegisterForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formRegister';

	/**
	 * Name of the username field
	 * @var string
	 */
	const FIELD_USERNAME = 'username';

	/**
	 * Name of the email field
	 * @var string
	 */
	const FIELD_EMAIL = 'email';

	/**
	 * Name of the password field
	 * @var string
	 */
	const FIELD_PASSWORD = 'password';

	/**
	 * Name of the password confirmation field
	 * @var string
	 */
	const FIELD_PASSWORD_CONFIRM = 'passwordConfirm';

	/**
	 * Translation key for the submit button
	 * @var string
	 */
	const TRANSLATION_SUBMIT = 'joppa.security.button.register';

	/**
	 * Constructs a new password form
	 * @param string $action URL where this form will point to
	 * @return null
	 */
	public function __construct($action) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

		$fieldFactory = FieldFactory::getInstance();
		$requiredValidator = new RequiredValidator();

		$usernameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_USERNAME);
		$usernameField->addValidator($requiredValidator);

		$emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL);
		$emailField->addValidator($requiredValidator);

		$passwordField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD);
		$passwordField->addValidator($requiredValidator);

		$passwordConfirmField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD_CONFIRM);
		$passwordConfirmField->addValidator($requiredValidator);

		$this->addField($usernameField);
		$this->addField($emailField);
		$this->addField($passwordField);
		$this->addField($passwordConfirmField);

		$this->addFormValidator(new NewPasswordFormValidator());
	}

    /**
     * Gets the username of this form
     * @return string
     */
    public function getUsername() {
        return $this->getValue(self::FIELD_USERNAME);
    }

    /**
     * Gets the email address of this form
     * @return string
     */
    public function getEmail() {
        return $this->getValue(self::FIELD_EMAIL);
    }

	/**
	 * Gets the password of this form
	 * @return string
	 */
	public function getPassword() {
	    return $this->getValue(self::FIELD_PASSWORD);
	}

	/**
	 * Gets the repeated password of this form
	 * @return string
	 */
	public function getPasswordConfirmation() {
	    return $this->getValue(self::FIELD_PASSWORD_CONFIRM);
	}

}