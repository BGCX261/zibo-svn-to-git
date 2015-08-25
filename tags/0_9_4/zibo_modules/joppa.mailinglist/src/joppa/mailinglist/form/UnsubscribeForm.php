<?php

namespace joppa\mailinglist\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\validation\ValidationFactory;

/**
 * Form to unsubscribe from the mailinglist
 */
class UnsubscribeForm extends Form {

	/**
	 * Name of this form
	 * @var string
	 */
	const NAME = 'formUnsubscribe';

	/**
	 * Name of the email field
	 * @var string
	 */
	const FIELD_EMAIL = 'email';

	/**
	 * Name of the unsubscribe button
	 * @var string
	 */
	const BUTTON_UNSUBSCRIBE = 'unsubscribe';

	/**
	 * Translation key for the unsubscribe button
	 * @var string
	 */
	const TRANSLATION_UNSUBSCRIBE = 'joppa.mailinglist.button.unsubscribe';

	/**
	 * Constructs a new unsubscribe form
	 * @param string $action URL where this form will point to
	 * @return null
	 */
	public function __construct($action) {
		parent::__construct($action, self::NAME);

		$this->appendToClass('data');

		$fieldFactory = FieldFactory::getInstance();

		$emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL);
		$unsubscribeButton = $fieldFactory->createSubmitField(self::BUTTON_UNSUBSCRIBE, self::TRANSLATION_UNSUBSCRIBE);

		$this->addField($emailField);
		$this->addField($unsubscribeButton);

		$validatorFactory = ValidationFactory::getInstance();
		$this->addValidator(self::FIELD_EMAIL, $validatorFactory->createValidator('required'));
	}

	/**
	 * Gets the email address from the form
	 * @return string
	 */
	public function getEmail() {
		return $this->getValue(self::FIELD_EMAIL);
	}

}