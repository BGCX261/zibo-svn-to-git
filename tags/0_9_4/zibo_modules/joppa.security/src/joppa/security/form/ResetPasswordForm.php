<?php

namespace joppa\security\form;

use joppa\security\form\validator\ResetPasswordFormValidator;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to request the username or email address of a user who wants to reset his password
 */
class ResetPasswordForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formResetPassword';

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
	 * Constructs a new password reset form
	 * @param string $action URL where this form will point to
	 * @return null
	 */
	public function __construct($action) {
		parent::__construct($action, self::NAME);

		$fieldFactory = FieldFactory::getInstance();

		$usernameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_USERNAME);

		$emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL);

		$this->addField($usernameField);
		$this->addField($emailField);

		$this->addFormValidator(new ResetPasswordFormValidator());
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

}