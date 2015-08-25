<?php

namespace joppa\forum\view;

use joppa\forum\model\data\ForumProfileData;

/**
 * Frontend view of a forum board
 */
class ForumProfileView extends ForumView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/profile';

	/**
	 * Constructs a new view for a forum board
	 * @param joppa\forum\model\data\ForumProfileData $profile
	 * @return null
	 */
	public function __construct(ForumProfileData $profile) {
		parent::__construct(self::TEMPLATE);

		$this->set('profile', $profile);
	}

}