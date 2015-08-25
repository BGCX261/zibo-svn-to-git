<?php

namespace joppa\comment\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a comment
 */
class CommentData extends Data {

	/**
	 * The parent comment
	 * @var integer|CommentData
	 */
    public $parent;

    /**
     * Replies to this comment
     * @var array
     */
    public $replies;

    /**
     * The object type
     * @var string
     */
    public $objectType;

    /**
     * The id of the object
     * @var string
     */
    public $objectId;

    /**
     * The name of the author
     * @var string
     */
    public $name;

    /**
     * The email address of the author
     * @var string
     */
    public $email;

    /**
     * The comment text
     * @var string
     */
    public $comment;

    /**
     * Link with the user of the author
     * @var integer|zibo\library\security\model\User
     */
    public $author;

    /**
     * The locale of the author when posting the comment
     * @var string
     */
    public $locale;

    /**
     * The IP address of the author when posting the comment
     * @var string
     */
    public $ip;

}