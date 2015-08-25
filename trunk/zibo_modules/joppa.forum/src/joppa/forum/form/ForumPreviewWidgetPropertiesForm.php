<?php

namespace joppa\forum\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form for the properties of the forum preview widget
 */
class ForumPreviewWidgetPropertiesForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formForumPreviewWidgetProperties';

	/**
	 * Name of the posts field
	 * @var string
	 */
	const FIELD_POSTS = 'posts';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new forum preview properties form
	 * @param string $action URL where this form will point to
	 * @param integer $posts Number of posts to preset the form
	 * @return null
	 */
	public function __construct($action, $posts) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$fieldFactory = FieldFactory::getInstance();

		$postsField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_POSTS, $posts);

		$this->addField($postsField);
	}

	/**
	 * Gets the posts per page
	 * @return integer
	 */
	public function getPosts() {
		return $this->getValue(self::FIELD_POSTS);
	}

}