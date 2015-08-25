<?php

namespace joppa\text\form;

use joppa\text\model\data\TextData;
use joppa\text\model\TextModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\ModelManager;

/**
 * Form to manage the properties of the text widget
 */
class TextPropertiesForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formTextProperties';

	/**
	 * Name of the id field
	 * @var string
	 */
	const FIELD_ID = 'id';

	/**
	 * Name of the text field
	 * @var string
	 */
	const FIELD_TEXT = 'text';

	/**
	 * Name of the version field
	 * @var string
	 */
	const FIELD_VERSION = 'version';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\text\model\data\TextData $text The text to edit
     * @return null
	 */
	public function __construct($action, TextData $text) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$fieldFactory = FieldFactory::getInstance();

		$idField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID, $text->id);
		$textField = $fieldFactory->createField(FieldFactory::TYPE_WYSIWYG, self::FIELD_TEXT, $text->text);
		$versionField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_VERSION, $text->version);

		$this->addField($idField);
		$this->addField($textField);
		$this->addField($versionField);
	}

	/**
	 * Gets the text submitted by this form
	 * @return joppa\text\model\TextData
	 */
	public function getText() {
        $textModel = ModelManager::getInstance()->getModel(TextModel::NAME);

        $text = $textModel->createData(false);
        $text->id = $this->getValue(self::FIELD_ID);
        $text->text = $this->getValue(self::FIELD_TEXT);
        $text->version = $this->getValue(self::FIELD_VERSION);

        return $text;
	}

}