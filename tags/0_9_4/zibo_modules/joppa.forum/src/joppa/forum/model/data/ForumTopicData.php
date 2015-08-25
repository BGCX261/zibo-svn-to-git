<?php

namespace joppa\forum\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a forum topic. A forum topic contains posts, most commonly on the same subject.
 * It belongs to a board.
 */
class ForumTopicData extends Data {

	/**
	 * The board of this topic
	 * @var integer|ForumBoardData
	 */
	public $board;

	/**
	 * The posts of this topic. An array of ForumPostData or their id's
	 * @var array
	 */
	public $posts;

	/**
	 * Number of pages in this topic (not part of the model)
	 * @var integer
	 */
	public $pages;

	/**
	 * The views of this topic. An array with UserData or their id's
	 * @var array
	 */
	public $views;

	/**
	 * The total number of people who have viewed this topic
	 * @var integer
	 */
	public $numViews;

	/**
	 * The total number of posts in this topic
	 * @var integer
	 */
	public $numPosts;

	/**
	 * The first post of this topic
	 * @var integer|ForumPostData
	 */
	public $firstPost;

	/**
	 * The last post of this topic
	 * @var integer|ForumPostData
	 */
	public $lastPost;

	/**
	 * Flag to see if this topic is sticky in it's board
	 * @var boolean
	 */
	public $isSticky;

	/**
	 * Flag to see if this topic is locked. A locked topic can no longer be modified
	 * @var boolean
	 */
	public $isLocked;

	/**
	 * Timestamp of the creation date
	 * @var integer
	 */
	public $dateAdded;

	/**
	 * Timestamp when this topic was last modified
	 * @var integer
	 */
	public $dateModified;

}