<?php

namespace joppa\forum\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a forum post. A forum post is the contribution of the users and what makes it all
 * alive. A forum post is posted in a forum topic.
 */
class ForumPostData extends Data {

	/**
	 * The author of this post
	 * @var integer|UserData
	 */
	public $author;

	/**
	 * The topic of this post
	 * @var integer|ForumTopicData
	 */
	public $topic;

	/**
	 * The number of this post in the topic
	 * @var integer
	 */
	public $topicPostNumber;

	/**
	 * The subject of this post
	 * @var string
	 */
	public $subject;

	/**
	 * The message of this post
	 * @var string
	 */
	public $message;

	/**
	 * The author who last modified this post
	 * @var integer|UserData
	 */
	public $authorModified;

	/**
	 * The timestamp when this post was created
	 * @var integer
	 */
	public $dateAdded;

	/**
	 * The timestamp when this post was last modified
	 * @var integer
	 */
	public $dateModified;

   /**
     * The URL to this profile
     * @var string
     */
    public $url;

}