<?php

namespace zibo\database\admin\form;

use zibo\database\admin\model\Connection;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form to select the default database connection
 */
class ConnectionDefaultForm extends Form {

    /**
     * Name of the form
     * @var string
     */
	const NAME = "formDatabaseDefaultConnection";

	/**
	 * Name of the default connection field
	 * @var string
	 */
	const FIELD_DEFAULT = 'default';

	/**
	 * Name of the save button
	 * @var string
	 */
	const BUTTON_SAVE = 'save';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new default database connection form
	 * @param string $formAction URL where this form will point to
	 * @param array $connections Available connections: array with Connection objects
	 * @param zibo\database\admin\model\Connection $defaultConnection The default connection
	 * @return null
	 */
	public function __construct($formAction, array $connections, Connection $defaultConnection) {
		parent::__construct($formAction, self::NAME);

		$defaultConnectionName = $defaultConnection->getName();

		$connectionOptions = array();
		foreach ($connections as $connection) {
			$connectionOptions[$connection->getName()] = $connection->getName();
		}

		$fieldFactory = FieldFactory::getInstance();

		$connectionField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_DEFAULT, $defaultConnectionName);
		$connectionField->setOptions($connectionOptions);

        $saveButton = $fieldFactory->createSubmitField(self::BUTTON_SAVE, self::TRANSLATION_SAVE);

        $this->addField($connectionField);
        $this->addField($saveButton);
	}

	/**
     * Gets the name of the submitted default connection
     * @return string
	 */
	public function getDefaultConnectionName() {
		return $this->getValue(self::FIELD_DEFAULT);
	}

}