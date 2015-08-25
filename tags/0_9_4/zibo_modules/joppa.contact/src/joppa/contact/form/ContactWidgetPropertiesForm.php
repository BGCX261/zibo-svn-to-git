<?php

namespace joppa\contact\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to manage the properties of the contact widget
 */
class ContactWidgetPropertiesForm extends Form {

	/**
	 * Name of this form
	 * @var string
 	 */
	const NAME = 'formContactFormWidgetProperties';

	/**
	 * Name of the recipient field
	 * @var string
	 */
	const FIELD_RECIPIENT = 'recipient';

	/**
	 * Name of the subject field
	 * @var string
	 */
	const FIELD_SUBJECT = 'subject';

	/**
	 * Translation key of the submit button
	 * @var string
	 */
	const TRANSLATION_SUBMIT = 'button.save';

	/**
	 * Constructs a new properties form for the contact widget
	 * @param unknown_type $action
	 * @param unknown_type $recipient
	 * @param unknown_type $subject
	 */
	public function __construct($action, $recipient, $subject) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

		$fieldFactory = FieldFactory::getInstance();

		$recipientField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_RECIPIENT, $recipient);
		$subjectField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_SUBJECT, $subject);

		$this->addField($recipientField);
		$this->addField($subjectField);

		$requiredValidator = new RequiredValidator();
		$this->addValidator(self::FIELD_RECIPIENT, $requiredValidator);
		$this->addValidator(self::FIELD_SUBJECT, $requiredValidator);
	}

	/**
	 * Gets the submitted recipient
	 * @return string
	 */
	public function getRecipient() {
	    return $this->getValue(self::FIELD_RECIPIENT);
	}

	/**
	 * Gets the submitted subject
	 * @return string
	 */
	public function getSubject() {
	    return $this->getValue(self::FIELD_SUBJECT);
	}

}