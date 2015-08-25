<?php

namespace joppa\forum\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a user's forum profile.
 */
class ForumProfileData extends Data {

	/**
	 * User of this profile
	 * @var integer|UserData
	 */
	public $user;

	/**
	 * Name of the user
	 * @var string
	 */
	public $name;

	/**
	 * Location of the user
	 * @var string
	 */
	public $location;

	/**
	 * Website of the user
	 * @var string
	 */
	public $website;

	/**
	 * Gender of the user
	 * @var string
	 */
	public $gender;

	/**
	 * Timestamp of the user's birthday
	 * @var integer
	 */
	public $birthday;

	/**
	 * Email address of the user's MSN account
	 * @var string
	 */
	public $msn;

	/**
	 * URL of the user's MySpace
	 * @var string
	 */
	public $myspace;

	/**
	 * URL of the user's facebook
	 * @var string
	 */
	public $facebook;

	/**
	 * Signature to display underneath the user's posts
	 * @var string
	 */
	public $signature;

	/**
	 * Total number of posts this user has posted
	 * @var integer
	 */
	public $numPosts;

	/**
	 * The ranking of this profile
	 * @var ForumRankingData
	 */
	public $ranking;

	/**
	 * The URL to this profile
	 * @var string
	 */
	public $url;

	/**
	 * Sets the ranking to this profile
	 * @param array $rankings Array with ForumRankingData objects
	 * @return null
	 */
	public function setRanking(array $rankings) {
		$this->ranking = null;

		foreach ($rankings as $ranking) {
			if ($this->numPosts < $ranking->numPosts) {
				break;
			}

			$this->ranking = $ranking;
		}
	}

}