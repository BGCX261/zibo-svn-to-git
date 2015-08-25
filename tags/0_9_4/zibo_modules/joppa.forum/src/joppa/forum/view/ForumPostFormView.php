<?php

namespace joppa\forum\view;

use joppa\forum\form\ForumPostForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the forum post form
 */
class ForumPostFormView extends SmartyView {

	/**
	 * The path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/post.form';

	/**
	 * Constructs a new forum post form view
	 * @param ForumPostForm $form
	 * @param unknown_type $title
	 * @param unknown_type $preview
	 * @return null
	 */
	public function __construct(ForumPostForm $form, $title = null, $preview = null) {
		$messageField = $form->getField(ForumPostForm::FIELD_MESSAGE);
		$emoticonParser = $messageField->getEmoticonParser();

		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
		$this->set('title', $title);
		$this->set('preview', $preview);
		$this->set('emoticonParser', $emoticonParser);
	}

}