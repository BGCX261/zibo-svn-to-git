<?php

namespace joppa\security\form;

use joppa\security\form\validator\NewPasswordFormValidator;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form of the Password Reset widget
 */
class NewPasswordForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formNewPassword';

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
	 * Constructs a new password form
	 * @param string $action URL where this form will point to
	 * @return null
	 */
	public function __construct($action) {
		parent::__construct($action, self::NAME);

		$fieldFactory = FieldFactory::getInstance();
		$requiredValidator = new RequiredValidator();

		$passwordField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD);
		$passwordField->addValidator($requiredValidator);

		$passwordConfirmField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD_CONFIRM);
		$passwordConfirmField->addValidator($requiredValidator);

		$this->addField($passwordField);
		$this->addField($passwordConfirmField);

		$this->addFormValidator(new NewPasswordFormValidator());
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