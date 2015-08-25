<?php

namespace joppa\forum\form;

use joppa\forum\model\data\ForumSearchData;
use joppa\forum\model\ForumCategoryModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\orm\ModelManager;

/**
 * Form to search the forum
 */
class ForumSearchForm extends Form {

	/**
	 * Name of this form
	 * @var string
	 */
	const NAME = 'formForumSearch';

	/**
	 * Name of the query field
	 * @var string
	 */
	const FIELD_QUERY = 'query';

	/**
	 * Name of the board field
	 * @var string
	 */
	const FIELD_BOARD = 'board';

	/**
	 * Name of the search button
	 * @var string
	 */
	const BUTTON_SEARCH = 'search';

	/**
	 * Translation key for the search button
	 * @var string
	 */
	const TRANSLATION_SEARCH = 'joppa.forum.button.search';

	/**
	 * Constructs a new forum search form
	 * @param string $action URL where this form will point to
	 * @param joppa\forum\model\data\ForumSearchData $data The data to preset the form
	 * @return null
	 */
	public function __construct($action, ForumSearchData $data) {
		parent::__construct($action, self::NAME);

		$modelManager = ModelManager::getInstance();
		$fieldFactory = FieldFactory::getInstance();

		$categoryModel = $modelManager->getModel(ForumCategoryModel::NAME);
		$categories = $categoryModel->getCategories();

		$queryField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_QUERY, $data->query);

		$boardField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_CATEGORY, $data->category);
		$boardField->setIsMultiple(true);
		foreach ($categories as $category) {
			$boards = array();
			foreach ($category->boards as $board) {
                $boards[$board->id] = $board->name;
			}


			$boardField->setOptions($boards, $category->name);
		}

		$searchButton = $fieldFactory->createSubmitField(self::BUTTON_SEARCH, self::TRANSLATION_SEARCH);

		$this->addField($queryField);
		$this->addField($boardField);
		$this->addField($searchField);
	}

	/**
	 * Gets the data of this form
	 * @return joppa\forum\model\data\ForumSearchData
	 */
	public function getData() {
		$data = new ForumSearchData();

		$data->query = $this->getValue(self::FIELD_QUERY);
		$data->board = $this->getValue(self::FIELD_BOARD);

		return $data;
	}

}