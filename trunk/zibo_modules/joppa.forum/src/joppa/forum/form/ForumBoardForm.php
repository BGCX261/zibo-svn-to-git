<?php

namespace joppa\forum\form;

use joppa\forum\model\data\ForumBoardData;
use joppa\forum\model\ForumBoardModel;
use joppa\forum\model\ForumCategoryModel;
use joppa\forum\model\ForumProfileModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\ModelManager;

/**
 * Form to edit a forum board
 */
class ForumBoardForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formForumBoard';

	/**
	 * Name of the id field
	 * @var string
	 */
	const FIELD_ID = 'id';

	/**
	 * Name of the category field
	 * @var string
	 */
	const FIELD_CATEGORY = 'category';

	/**
	 * Name of the name field
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Name of the description field
	 * @var string
	 */
	const FIELD_DESCRIPTION = 'description';

	/**
	 * Name of the allow new topics field
	 * @var string
	 */
	const FIELD_ALLOW_NEW_TOPICS = 'allowNewTopics';

	/**
	 * Name of the allow new posts field
	 * @var string
	 */
	const FIELD_ALLOW_NEW_POSTS = 'allowNewPosts';

	/**
	 * Name of the allow view field
	 * @var string
	 */
	const FIELD_ALLOW_VIEW = 'allowView';

	/**
	 * Name of the moderators field
	 * @var string
	 */
	const FIELD_MODERATORS = 'moderators';

	/**
	 * Translation key for the submit button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new forum board form
	 * @param string $action URL where this form will point to
	 * @param joppa\forum\model\data\ForumBoardData $board Board data to preset the form
	 */
	public function __construct($action, ForumBoardData $board = null) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$modelManager = ModelManager::getInstance();

		$categoryModel = $modelManager->getModel(ForumCategoryModel::NAME);
		$profileModel = $modelManager->getModel(ForumProfileModel::NAME);

		$category = null;

		if (!$board) {
            $boardModel = $modelManager->getModel(ForumBoardModel::NAME);
            $board = $boardModel->createData();
		} elseif (isset($board->category->id)) {
			$category = $board->category->id;
		}

		$allowOptions = ForumBoardModel::getAllowOptions();

		$fieldFactory = FieldFactory::getInstance();

		$idField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID, $board->id);

		$categoryField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_CATEGORY, $category);
		$categoryField->setOptions($categoryModel->getDataList());

		$nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME, $board->name);

		$descriptionField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_DESCRIPTION, $board->description);

		$allowNewTopicsField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_ALLOW_NEW_TOPICS, $board->allowNewTopics);
		$allowNewTopicsField->setOptions($allowOptions);

		$allowNewPostsField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_ALLOW_NEW_POSTS, $board->allowNewPosts);
		$allowNewPostsField->setOptions($allowOptions);

		$allowViewField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_ALLOW_VIEW, $board->allowView);
		$allowViewField->setOptions($allowOptions);

		$moderatorsField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_MODERATORS, $board->moderators);
		$moderatorsField->setOptions($profileModel->getModeratorList());
		$moderatorsField->setIsMultiple(true);

		$this->addField($idField);
		$this->addField($categoryField);
		$this->addField($nameField);
		$this->addField($descriptionField);
		$this->addField($allowViewField);
		$this->addField($allowNewTopicsField);
		$this->addField($allowNewPostsField);
		$this->addField($moderatorsField);
	}

	/**
	 * Gets the board of the form
	 * @return joppa\forum\model\data\ForumBoardData
	 */
	public function getBoard() {
		$board = ModelManager::getInstance()->getModel(ForumBoardModel::NAME)->createData(false);

		$board->id = $this->getValue(self::FIELD_ID);
		$board->category = $this->getValue(self::FIELD_CATEGORY);
		$board->name = $this->getValue(self::FIELD_NAME);
		$board->description = $this->getValue(self::FIELD_DESCRIPTION);
		$board->allowView = $this->getValue(self::FIELD_ALLOW_VIEW);
		$board->allowNewTopics = $this->getValue(self::FIELD_ALLOW_NEW_TOPICS);
		$board->allowNewPosts = $this->getValue(self::FIELD_ALLOW_NEW_POSTS);
		$board->moderators = array_keys($this->getValue(self::FIELD_MODERATORS));

		return $board;
	}

}