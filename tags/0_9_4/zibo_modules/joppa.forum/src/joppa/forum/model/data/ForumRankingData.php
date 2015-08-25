<?php

namespace joppa\forum\model\data;

use joppa\forum\model\ForumRankingModel;

use zibo\library\html\Image;
use zibo\library\orm\model\data\Data;

/**
 * Data container of a user's forum ranking. A forum ranking defines the number of stars a user
 * gets when he has reached a certain number of posts.
 */
class ForumRankingData extends Data {

	/**
	 * The name of this ranking
	 * @var string
	 */
	public $name;

	/**
	 * Number of posts needed for this ranking
	 * @var integer
	 */
	public $numPosts;

	/**
	 * Number of stars this rankings gives to a user
	 * @var integer
	 */
	public $stars;

	public function getStarsHtml() {
		if ($this->stars) {
			$image = new Image(ForumRankingModel::STAR);
			$star = $image->getHtml();
		}

		$html = '';
        for ($i = 0; $i < $this->stars; $i++) {
            $html .= $star;
        }

		return $html;
	}

}