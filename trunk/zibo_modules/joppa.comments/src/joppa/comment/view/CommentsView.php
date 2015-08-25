<?php

namespace joppa\comment\view;

use joppa\comment\form\CommentForm;
use joppa\comment\model\data\CommentData;

use zibo\library\security\model\User;
use zibo\library\smarty\view\SmartyView;

/**
 * View for the comments
 */
class CommentsView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/comment/index';

	/**
	 * Constructs a new comments view
	 * @param array $comments Array with the comments to display
	 * @param CommentForm $form The comment form
	 * @param string $title
	 * @param string $replyUrl
	 * @param string $editUrl
	 * @param zibo\library\security\model\User $user
	 * @param boolean $isAdmin
	 * @return null
	 */
    public function __construct(array $comments, CommentForm $form = null, $title = null, $replyUrl = null, $editUrl = null, $deleteUrl = null, User $user = null, $isAdmin = null, CommentData $parent = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('comments', $comments);
        $this->set('form', $form);
        $this->set('title', $title);

        $this->set('replyUrl', $replyUrl);
        $this->set('editUrl', $editUrl);
        $this->set('deleteUrl', $deleteUrl);

        $this->set('user', $user);
        $this->set('isAdmin', $isAdmin);

        $this->set('parent', $parent);
    }

}