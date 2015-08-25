<?php

namespace joppa\forum\model\data;

use joppa\forum\model\ForumBoardModel;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a forum board. A forum board is the second node in the forum's hierarchy. It
 * contains topics with their posts.
 */
class ForumBoardData extends Data {

	/**
	 * Category of this board
	 * @var integer|ForumCategoryData
	 */
	public $category;

	/**
	 * Name of this board
	 * @var string
	 */
	public $name;

	/**
	 * Description of this board
	 * @var string
	 */
	public $description;

	/**
	 * Flag to see whether this board allows new topics and by who
	 * @var integer
	 */
	public $allowNewTopics;

	/**
	 * Flag to see whether this board allows new posts
	 * @var integer
	 */
	public $allowNewPosts;

	/**
	 * Flag to see whether this board allows view
	 * @var integer
	 */
	public $allowView;

	/**
	 * The moderators of this board. An array of UserData objects or their id's
	 * @var array
	 */
	public $moderators;

	/**
	 * The topics of this board
	 * @var array
	 */
	public $topics;

	/**
	 * The last added or modified topic in this board
	 * @var integer|ForumTopicData
	 */
	public $lastTopic;

	/**
	 * Number of topics in this board
	 * @var integer
	 */
	public $numTopics;

	/**
	 * Number of posts in this board
	 * @var integer
	 */
	public $numPosts;

	/**
     * Index of this board in the category
     * @var integer
	 */
	public $orderIndex;

	/**
	 * Checks whether the provided user is allowed to view this topic
	 * @param ForumProfileData $profile
	 * @return boolean True if a view is allowed, false otherwise
	 */
	public function isViewAllowed($profile) {
		return ForumBoardModel::isAllowed($this->allowView, $profile);
	}

	/**
	 * Checks whether the provided user is allowed to create a new topic
	 * @param ForumProfileData $profile
	 * @return boolean True if a new topic is allowed, false otherwise
	 */
	public function isNewTopicAllowed($profile) {
		return ForumBoardModel::isAllowed($this->allowNewTopics, $profile);
	}

	/**
	 * Checks whether the provided user is allowed to create a new topic
	 * @param ForumProfileData $profile
	 * @return boolean True if a new topic is allowed, false otherwise
	 */
	public function isNewPostAllowed($profile) {
		return ForumBoardModel::isAllowed($this->allowNewPosts, $profile);
	}

}