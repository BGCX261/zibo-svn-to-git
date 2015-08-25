<?php

namespace joppa\forum\form;

use joppa\forum\model\data\ForumCategoryData;
use joppa\forum\model\ForumCategoryModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\ModelManager;

/**
 * Form for a forum category
 */
class ForumCategoryForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formForumCategory';

    /**
     * Name of the id field
     * @var string
     */
	const FIELD_ID = 'id';

	/**
	 * Name of the name field
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new forum category form
	 * @param string $action URL where this form will point to
	 * @param joppa\forum\model\data\ForumCategoryData $category Forum category to preset the form
	 * @return null
	 */
	public function __construct($action, ForumCategoryData $category = null) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$fieldFactory = FieldFactory::getInstance();

		$id = null;
		$name = null;

		if ($category) {
			$id = $category->id;
			$name = $category->name;
		}

		$idField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID, $id);

		$nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME, $name);

		$this->addField($idField);
		$this->addField($nameField);
	}

	/**
	 * Gets the forum category from the form
	 * @return joppa\forum\model\data\ForumCategoryData
	 */
	public function getCategory() {
		$category = ModelManager::getInstance()->getModel(ForumCategoryModel::NAME)->createData(false);

		$category->id = $this->getValue(self::FIELD_ID);
		$category->name = $this->getValue(self::FIELD_NAME);

		return $category;
	}

}