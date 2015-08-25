<?php

namespace joppa\forum\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form for the properties of the forum widget
 */
class ForumWidgetPropertiesForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formForumWidgetProperties';

	/**
	 * Name of the topics per page field
	 * @var string
	 */
	const FIELD_TOPICS_PER_PAGE = 'topicsPerPage';

	/**
	 * Name of the posts per page field
	 * @var string
	 */
	const FIELD_POSTS_PER_PAGE = 'postsPerPage';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new forum category form
	 * @param string $action URL where this form will point to
	 * @return null
	 */
	public function __construct($action, $topicsPerPage, $postsPerPage) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$fieldFactory = FieldFactory::getInstance();

		$topicsPerPageField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_TOPICS_PER_PAGE, $topicsPerPage);

		$postsPerPageField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_POSTS_PER_PAGE, $postsPerPage);

		$this->addField($topicsPerPageField);
		$this->addField($postsPerPageField);
	}

	/**
	 * Gets the topics per page
	 * @return integer
	 */
	public function getTopicsPerPage() {
		return $this->getValue(self::FIELD_TOPICS_PER_PAGE);
	}

	/**
	 * Gets the posts per page
	 * @return integer
	 */
	public function getPostsPerPage() {
		return $this->getValue(self::FIELD_POSTS_PER_PAGE);
	}

}