<?php

namespace joppa\comment\form;

use joppa\comment\model\data\CommentData;
use joppa\comment\model\CommentModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\orm\ModelManager;

/**
 * Form to add or edit a comment
 */
class CommentForm extends Form {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formComment';

    /**
     * Name of the field id
     * @var string
     */
    const FIELD_ID = 'id';

    /**
     * Name of the parent comment id
     * @var string
     */
    const FIELD_PARENT = 'parent';

    /**
     * Name of the version field
     * @var string
     */
    const FIELD_VERSION = 'version';

    /**
     * Name of the name field
     * @var string
     */
    const FIELD_NAME = 'name';

    /**
     * Name of the e-mail field
     * @var stirng
     */
    const FIELD_EMAIL = 'email';

    /**
     * Name of the comment field
     * @var string
     */
    const FIELD_COMMENT = 'comment';

    /**
     * Name of the submit button
     * @var string
     */
    const BUTTON_SUBMIT  = 'submit';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'button.submit';

    /**
     * Construct a new comment form
     * @param string $action URL where the form will point to
     * @param joppa\comment\model\data\CommentData $comment comment to preset the form
     * @return null
     */
    public function __construct($action, CommentData $comment) {
        parent::__construct($action, self::NAME);

        $fieldFactory = FieldFactory::getInstance();

        $idField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID, $comment->id);
        $parentField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_PARENT, $comment->parent);
        $versionField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_VERSION, $comment->version);
        $nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME, $comment->name);
        $emailField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_EMAIL, $comment->email);
        $commentField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_COMMENT, $comment->comment);
        $submitButton = $fieldFactory->createSubmitField(self::BUTTON_SUBMIT, self::TRANSLATION_SUBMIT);

        $this->addField($idField);
        $this->addField($parentField);
        $this->addField($versionField);
        $this->addField($nameField);
        $this->addField($emailField);
        $this->addField($commentField);
        $this->addField($submitButton);
    }

    /**
     * Get the comment which is set to the form
     * @return joppa\widget\model\Comment
     */
    public function getComment() {
        $comment = ModelManager::getInstance()->getModel(CommentModel::NAME)->createData(false);

        $comment->id = $this->getValue(self::FIELD_ID);
        $comment->parent = $this->getValue(self::FIELD_PARENT);
        $comment->name = $this->getValue(self::FIELD_NAME);
        $comment->email = $this->getValue(self::FIELD_EMAIL);
        $comment->comment = $this->getValue(self::FIELD_COMMENT);

        return $comment;
    }

}