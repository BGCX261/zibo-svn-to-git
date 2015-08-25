<?php

namespace joppa\forum\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a forum search.
 */
class ForumSearchData extends Data {

	/**
	 * The query string
	 * @var string
	 */
	public $query;

	/**
	 * The board(s) for the search
	 * @var integer|array
	 */
	public $board;

}