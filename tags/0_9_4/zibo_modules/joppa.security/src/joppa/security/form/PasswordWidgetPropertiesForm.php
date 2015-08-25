<?php

namespace joppa\security\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to set the properties of the Password Retrieval widget
 */
class PasswordWidgetPropertiesForm extends SubmitCancelForm {

	const NAME = 'formPasswordWidgetProperties';

	const FIELD_SUBJECT = 'subject';

	const FIELD_MESSAGE = 'message';

	const TRANSLATION_SUBMIT = 'button.save';

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

	public function getSubject() {
	    return $this->getValue(self::FIELD_SUBJECT);
	}

	public function getMessage() {
	    return $this->getValue(self::FIELD_MESSAGE);
	}

}