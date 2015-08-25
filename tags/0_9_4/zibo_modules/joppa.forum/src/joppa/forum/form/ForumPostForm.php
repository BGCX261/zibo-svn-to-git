<?php

namespace joppa\forum\form;

use joppa\forum\model\data\ForumPostData;
use joppa\forum\model\ForumPostModel;

use zibo\library\emoticons\EmoticonParser;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\ModelManager;

/**
 * Form of a forum post
 */
class ForumPostForm extends SubmitCancelForm {

	/**
	 * Name of this form
	 * @var string
	 */
	const NAME = 'formForumPost';

	/**
	 * Name of the id field
	 * @var string
	 */
	const FIELD_ID = 'id';

	/**
	 * Name of the subject field
	 * @var string
	 */
	const FIELD_SUBJECT = 'subject';

	/**
	 * Name of the message field
	 * @var string
	 */
	const FIELD_MESSAGE = 'message';

	/**
	 * Name of the preview button
	 * @var string
	 */
	const BUTTON_PREVIEW = 'preview';

	/**
	 * Translation key for the preview button
	 * @var string
	 */
	const TRANSLATION_PREVIEW = 'joppa.forum.button.preview';

    /**
     * The field type of the message field
     * @var string
     */
    const TYPE_FIELD_MESSAGE = 'bbcode';

	/**
	 * Constructs a new forum post form
	 * @param string $action URL where this form will point to
	 * @param joppa\forum\model\data\ForumPostData $post The post to preset the form
	 * @param zibo\library\emoticons\EmoticonParser $emoticonParser Emoticon parser for the BBCode field
	 * @return null
	 */
	public function __construct($action, ForumPostData $post, EmoticonParser $emoticonParser = null) {
		parent::__construct($action, self::NAME);

		$fieldFactory = FieldFactory::getInstance();

		$idField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID, $post->id);

		$subjectField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_SUBJECT, $post->subject);

		$messageField = $fieldFactory->createField(self::TYPE_FIELD_MESSAGE, self::FIELD_MESSAGE, $post->message);
		$messageField->setEmoticonParser($emoticonParser);

		$previewButton = $fieldFactory->createSubmitField(self::BUTTON_PREVIEW, self::TRANSLATION_PREVIEW);

		$this->addField($idField);
		$this->addField($subjectField);
		$this->addField($messageField);
		$this->addField($previewButton);
	}

	/**
	 * Gets whether the preview button is submitted
	 * @return boolean
	 */
	public function isPreview() {
		return $this->getValue(self::BUTTON_PREVIEW) ? true : false;
	}

	/**
	 * Gets the post of this form
	 * @return joppa\forum\model\data\ForumPostData
	 */
	public function getPost() {
		$post = ModelManager::getInstance()->getModel(ForumPostModel::NAME)->createData(false);

		$post->id = $this->getValue(self::FIELD_ID);
		$post->subject = $this->getValue(self::FIELD_SUBJECT);
		$post->message = $this->getValue(self::FIELD_MESSAGE);

		return $post;
	}

}