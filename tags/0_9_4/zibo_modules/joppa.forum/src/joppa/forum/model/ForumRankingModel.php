<?php

namespace joppa\forum\model;

use zibo\library\orm\model\ExtendedModel;

/**
 * Model of the forum rankings
 */
class ForumRankingModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ForumRanking';

	/**
	 * Path to the image of a star
	 * @var string
	 */
	const STAR = 'web/images/forum/star.png';

	/**
	 * Gets all the rankings
	 * @return array Array with ForumRankingData objects
	 */
	public function getRankings() {
		$query = $this->createQuery();
		$query->addOrderBy('{numPosts} ASC');

		return $query->query();
	}

}