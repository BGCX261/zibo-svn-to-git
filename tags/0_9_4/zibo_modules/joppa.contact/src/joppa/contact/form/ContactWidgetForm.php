<?php

namespace joppa\contact\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Contact form
 */
class ContactWidgetForm extends Form {

	/**
	 * Name of this form
	 * @var string
	 */
	const NAME = 'formContactWidget';

	/**
	 * Name of the name field
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Name of the email field
	 * @var string
	 */
	const FIELD_EMAIL = 'email';

	/**
	 * Name of the message field
	 * @var string
	 */
	const FIELD_MESSAGE = 'message';

	/**
	 * Name of the submit button
	 * @var string
	 */
	const BUTTON_SUBMIT = 'submit';

	/**
	 * Translation key for the submit button
	 * @var string
	 */
	const TRANSLATION_SUBMIT = 'joppa.contact.button.send';

	/**
	 * Constructs a new contact form
	 * @param string $action URL where this form will point to
	 * @param string $name Name of the sender
	 * @param string $email Email address of the sender
	 * @param string $message Message of the sender
	 * @return null
	 */
	public function __construct($action, $name = null, $email = null, $message = null) {
		parent::__construct($action, self::NAME);

		$fieldFactory = FieldFactory::getInstance();

		$nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME, $name);
		$emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL, $email);
		$messageField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_MESSAGE, $message);
		$submitButton = $fieldFactory->createSubmitField(self::BUTTON_SUBMIT, self::TRANSLATION_SUBMIT);

		$this->addField($nameField);
		$this->addField($emailField);
		$this->addField($messageField);
		$this->addField($submitButton);

		$requiredValidator = new RequiredValidator();
		$this->addValidator(self::FIELD_NAME, $requiredValidator);
		$this->addValidator(self::FIELD_EMAIL, $requiredValidator);
		$this->addValidator(self::FIELD_MESSAGE, $requiredValidator);
	}

	/**
	 * Gets the submitted name
	 * @return string
	 */
	public function getName() {
	    return $this->getValue(self::FIELD_NAME);
	}

	/**
	 * Gets the submitted email address
	 * @return string
	 */
	public function getEmail() {
	    return $this->getValue(self::FIELD_EMAIL);
	}

	/**
	 * Gets the submitted message
	 * @return string
	 */
	public function getMessage() {
		return $this->getValue(self::FIELD_MESSAGE);
	}

}