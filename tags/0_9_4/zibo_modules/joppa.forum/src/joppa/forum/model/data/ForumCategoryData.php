<?php

namespace joppa\forum\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a forum category. A forum category is the first node for creating a forum's
 * hierarchy. It contains boards which contain the topics with their posts.
 */
class ForumCategoryData extends Data {

	/**
	 * Name of this category
	 * @var string
	 */
	public $name;

	/**
	 * The boards in this category. Array of ForumBoardData objects or their id's
	 * @var array
	 */
	public $boards;

	/**
	 * Index of this category
	 * @var integer
	 */
	public $orderIndex;

}