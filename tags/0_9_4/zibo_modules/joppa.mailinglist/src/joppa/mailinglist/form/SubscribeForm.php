<?php

namespace joppa\mailinglist\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\validation\ValidationFactory;

/**
 * Form to subscribe to the mailinglist
 */
class SubscribeForm extends Form {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formSubscribe';

	/**
	 * Name of the email field
	 * @var string
	 */
	const FIELD_EMAIL = 'email';

	/**
	 * Name of the subscribe button
	 * @var string
	 */
	const BUTTON_SUBSCRIBE = 'subscribe';

	/**
	 * Translation of the subscribe button
	 * @var string
	 */
	const TRANSLATION_SUBSCRIBE = 'joppa.mailinglist.button.subscribe';

	/**
	 * Constructs a new subscribe form
	 * @param string $action URL where this form will point to
	 * @return null
	 */
	public function __construct($action) {
		parent::__construct($action, self::NAME);

		$this->appendToClass('data');

		$fieldFactory = FieldFactory::getInstance();

		$emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL);
		$subscribeButton = $fieldFactory->createSubmitField(self::BUTTON_SUBSCRIBE, self::TRANSLATION_SUBSCRIBE);

		$this->addField($emailField);
		$this->addField($subscribeButton);

		$validatorFactory = ValidationFactory::getInstance();
		$this->addValidator(self::FIELD_EMAIL, $validatorFactory->createValidator('required'));
	}

	/**
	 * Gets the email address of this form
	 * @return string
	 */
	public function getEmail() {
		return $this->getValue(self::FIELD_EMAIL);
	}

}