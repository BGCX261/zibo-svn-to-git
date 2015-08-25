<?php

namespace joppa\security\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to set the properties of the Password Retrieval widget
 */
class RegisterWidgetPropertiesForm extends SubmitCancelForm {

	/**
	 * Name of this form
	 * @var string
	 */
	const NAME = 'formRegisterWidgetProperties';

	/**
	 * Name for the subject field
	 * @var string
	 */
	const FIELD_SUBJECT = 'subject';

	/**
	 * Name for the message field
	 * @var string
	 */
	const FIELD_MESSAGE = 'message';

	/**
	 * Translation key for the submit button
	 * @var string
	 */
	const TRANSLATION_SUBMIT = 'button.save';

	/**
	 * Constructs a new properties form
	 * @param string $action URL where this form will point to
	 * @param string $subject The subject
	 * @param string $message The message
	 * @return null
	 */
	public function __construct($action, $subject = null, $message = null) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

		$requiredValidator = new RequiredValidator();

		$fieldFactory = FieldFactory::getInstance();

		$subjectField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_SUBJECT, $subject);
		$subjectField->addValidator($requiredValidator);

		$messageField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_MESSAGE, $message);
		$messageField->addValidator($requiredValidator);

		$this->addField($subjectField);
		$this->addField($messageField);
	}

	/**
	 * Gets the subject of this form
	 * @return string
	 */
	public function getSubject() {
	    return $this->getValue(self::FIELD_SUBJECT);
	}

	/**
	 * Gets the message of this form
	 * @return string
	 */
	public function getMessage() {
	    return $this->getValue(self::FIELD_MESSAGE);
	}

}