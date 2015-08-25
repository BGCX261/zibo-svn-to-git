<?php

namespace joppa\comment\controller;

use joppa\comment\form\CommentForm;
use joppa\comment\model\data\CommentData;
use joppa\comment\model\CommentModel;
use joppa\comment\view\CommentsView;

use joppa\controller\JoppaWidget;

use joppa\model\NodeModel;

use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;

/**
 * Widget to add comments to a item
 */
class CommentsWidget extends JoppaWidget {

	/**
	 * Action to delete the comment
	 * @var string
	 */
	const ACTION_DELETE = 'deleteComment';

	/**
	 * Action to edit the comment
	 * @var string
	 */
	const ACTION_EDIT = 'editComment';

	/**
	 * Action to reply to a comment
	 * @var string
	 */
	const ACTION_REPLY = 'replyComment';

	/**
	 * Action to save the comment
	 * @var string
	 */
	const ACTION_SAVE = 'saveComment';

    /**
     * Path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/comments.png';

	/**
     * Translation key for the error when a comment is not found
     * @var string
     */
	const TRANSLATION_ERROR_NOT_FOUND = 'joppa.comment.error.not.found';

	/**
	 * Translation key for the error when a comment is being edited by an unauthorized person
	 * @var string
	 */
	const TRANSLATION_ERROR_NOT_ALLOWED_EDIT = 'joppa.comment.error.not.allowed.edit';

	/**
	 * Translation key for the error when a comment is being deleted by an unauthorized person
	 * @var string
	 */
	const TRANSLATION_ERROR_NOT_ALLOWED_DELETE = 'joppa.comment.error.not.allowed.delete';

	/**
	 * Translation key for the message when a comment has been deleted
	 * @var string
	 */
	const TRANSLATION_MESSAGE_DELETED = 'joppa.comment.message.deleted';

	/**
	 * Translation key for the message when a comment has been saved
	 * @var string
	 */
	const TRANSLATION_MESSAGE_SAVED = 'joppa.comment.message.saved';

	/**
	 * Translation key for the add comment title
	 * @var string
	 */
	const TRANSLATION_ADD = 'joppa.comment.title.add';

	/**
	 * Translation key for the edit comment title
	 * @var string
	 */
	const TRANSLATION_EDIT = 'joppa.comment.title.edit';

	/**
	 * Translation key for the reply comment title
	 * @var string
	 */
	const TRANSLATION_REPLY = 'joppa.comment.title.reply';

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.comment.widget.name';

	/**
	 * Hook with the orm module
	 * @var array
	 */
    public $useModels = CommentModel::NAME;

    /**
     * Name of the data type
     * @var stirng
     */
    private $objectType;

    /**
     * Id of the data
     * @var string
     */
    private $objectId;

    /**
     * The current user
     * @var zibo\library\security\model\User
     */
    private $user;

    /**
     * True if the user is a comment admin
     * @var boolean
     */
    private $isAdmin;

    /**
     * Construct this widget controller
     * @return null
     */
    public function __construct() {
    	parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Gets the possible action parameters for a comment request
     * @return array
     */
    public function getRequestParameters() {
    	return array(
    	   self::ACTION_DELETE,
    	   self::ACTION_EDIT,
    	   self::ACTION_REPLY,
    	   self::ACTION_SAVE,
    	);
    }

    /**
     * Set the data object of the comments
     * @param string $objectType name of the data type
     * @param int $objectId id of the data
     * @return null
     */
    public function setObject($objectType, $objectId) {
    	$this->objectType = $objectType;
    	$this->objectId = $objectId;
    }

    /**
     * Will get the profile of the current user and check if the user is a admin for the comments. Executes before every action.
     * @return null
     */
    public function preAction() {
    	$securityManager = SecurityManager::getInstance();

    	$this->user = $securityManager->getUser();
    	$this->isAdmin = $securityManager->isPermissionAllowed(CommentModel::PERMISSION_ADMIN);

    	if ($this->objectType) {
    		return;
    	}

    	$node = $this->getNode();

    	$this->setObject(NodeModel::NAME, $node->id);
    }

    /**
     * Action to show the comments of the current object
     * @return null
     */
    public function indexAction() {
        $this->setCommentsView();
    }

    /**
     * Action to edit a comment
     * @param int $id id of the comment
     * @return null
     */
    public function editCommentAction($id) {
        $comment = $this->models[CommentModel::NAME]->findById($id, 0);
        if (!$comment) {
            $this->addError(self::TRANSLATION_ERROR_NOT_FOUND);
            $this->setError404();
            return;
        }

        if (!$this->isAdmin && (!$this->user || $comment->author != $this->user->getUserId())) {
            $this->addError(self::TRANSLATION_ERROR_NOT_ALLOWED_EDIT);
            $this->setError404();
	    	$this->setCommentsView();
            return;
        }

    	$form = $this->createForm($comment);

    	$this->setCommentsView($form, self::TRANSLATION_EDIT);
    }

    /**
     * Action to reply to a comment, creates a new comment and sets the parent to it
     * @param int $id id of the comment
     * @return null
     */
    public function replyCommentAction($id) {
        $parent = $this->models[CommentModel::NAME]->findById($id);
        if (!$parent) {
            $this->addError(self::TRANSLATION_ERROR_NOT_FOUND);
    		$this->setError404();
            return;
        }

        $comment = $this->createComment();
        $comment->parent = $parent->id;

    	$form = $this->createForm($comment);

    	$this->setCommentsView($form, self::TRANSLATION_REPLY, $parent);
    }

    /**
     * Action to delete to a comment
     * @param int $id id of the comment
     * @return null
     */
    public function deleteCommentAction($id) {
        if (!$this->isAdmin) {
            $this->addError(self::TRANSLATION_ERROR_NOT_ALLOWED_DELETE);
    		$this->setError404();
    		$this->setCommentsView();
            return;
        }

        $comment = $this->models[CommentModel::NAME]->findById($id);
        if (!$comment) {
            $this->addError(self::TRANSLATION_ERROR_NOT_FOUND);
    		$this->setError404();
            return;
        }

        $this->models[CommentModel::NAME]->delete($comment);

        $this->addInformation(self::TRANSLATION_MESSAGE_DELETED);
		$this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Action to save the comment to the model
     * @return null
     */
    public function saveCommentAction() {
    	$form = $this->createForm();
    	if (!$form->isSubmitted()) {
			$this->response->setRedirect($this->request->getBasePath());
			return;
    	}

		try {
	    	$comment = $form->getComment();

			$comment->objectType = $this->objectType;
			$comment->objectId = $this->objectId;

			if (!$comment->id && $this->user) {
				$comment->author = $this->user->getUserId();
			}

			$this->models[CommentModel::NAME]->save($comment);

			$this->addInformation(self::TRANSLATION_MESSAGE_SAVED);
			$this->response->setRedirect($this->request->getBasePath());
			return;
		} catch (ValidationException $e) {
			$form->setException($e);
		}

		$this->setCommentsView($form);
    }

    /**
     * Set a view, which holds the comments interface, to the response
     * @param joppa\comment\form\CommentForm $form
     * @param string $title
     * @param joppa\comment\model\data\CommentData $parent
     * @return null
     */
    private function setCommentsView(CommentForm $form = null, $title = null, CommentData $parent = null) {
        if (!$form) {
            $form = $this->createForm();
        }

        if (!$title) {
        	$title = self::TRANSLATION_ADD;
        }

        $comments = $this->models[CommentModel::NAME]->getComments($this->objectType, $this->objectId);

        $basePath = $this->request->getBasePath();

        $replyUrl = $basePath . '/' . self::ACTION_REPLY . '/';
        $editUrl = null;
        $deleteUrl = null;
        if ($this->user) {
            $editUrl = $basePath . '/' . self::ACTION_EDIT . '/';

            if ($this->isAdmin) {
	            $deleteUrl = $basePath . '/' . self::ACTION_DELETE . '/';
            }
        }

        $view = new CommentsView($comments, $form, $title, $replyUrl, $editUrl, $deleteUrl, $this->user, $this->isAdmin, $parent);
        $this->response->setView($view);
    }

    /**
     * Create a new comment form
     * @param joppa\comment\model\data\CommentData $comment optional comment to preset the form
     * @return joppa\comment\form\CommentForm
     */
    private function createForm($comment = null) {
        if (!$comment) {
            $comment = $this->createComment();
        }

        return new CommentForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $comment);
    }

    /**
     * Create a new comment, if a user is logged in, set his/her name and email to the newly created comment
     * @return joppa\comment\model\data\CommentData
     */
    private function createComment() {
        $comment = $this->models[CommentModel::NAME]->createData();

        if ($this->user) {
            $comment->name = $this->user->getUserName();
            $comment->email = $this->user->getUserEmail();
        }

        return $comment;
    }

}