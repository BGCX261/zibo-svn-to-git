<?php

namespace zibo\database\admin\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\ValidationFactory;

/**
 * Form for the definition of a database connection
 */
class ConnectionForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = "formDatabaseConnection";

	/**
	 * Name of the name field
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Name of the DSN field
	 * @var string
	 */
	const FIELD_DSN = 'dsn';

	/**
	 * Name of the old name field
	 * @var string
	 */
	const FIELD_OLD_NAME = 'oldName';

	/**
	 * Translation key for the submit button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Translation key for the error of the name field
	 * @var string
	 */
	const TRANSLATION_VALIDATION_NAME = 'database.error.letters.numbers';

	/**
	 * Regular expression for the validator of the name field
	 * @var string
	 */
	const VALIDATION_NAME_REGEX = '/^([a-zA-Z0-9_])*$/';

	/**
	 * Constructs a new connection form
	 * @param string $formAction URL where this form will point to
	 * @param string $name Name of the connection
	 * @param string $dsn DSN string
	 * @param string $oldName Old name of the connection
	 * @return null
	 */
	public function __construct($formAction, $name = null, $dsn = null, $oldName = null) {
		parent::__construct($formAction, self::NAME, self::TRANSLATION_SAVE);

		$fieldFactory = FieldFactory::getInstance();

		$nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME, $name);

		$dsnField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DSN, $dsn);

		$oldNameField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_OLD_NAME, $oldName);

        $this->addField($nameField);
        $this->addField($dsnField);
        $this->addField($oldNameField);

        $validationFactory = ValidationFactory::getInstance();
        $nameField->addValidator($validationFactory->createValidator('required'));
        $nameField->addValidator($validationFactory->createValidator('regex', array('regex' => self::VALIDATION_NAME_REGEX, 'message' => self::TRANSLATION_VALIDATION_NAME)));
        $dsnField->addValidator($validationFactory->createValidator('dsn'));
	}

	/**
     * Gets the submitted name
     * @return string
	 */
	public function getName() {
		return $this->getValue(self::FIELD_NAME);
	}

	/**
     * Gets the submitted DSN
     * @return string
	 */
	public function getDsn() {
		return $this->getValue(self::FIELD_DSN);
	}

	/**
     * Gets the submitted old name
     * @return string
	 */
	public function getOldName() {
		return $this->getValue(self::FIELD_OLD_NAME);
	}

}