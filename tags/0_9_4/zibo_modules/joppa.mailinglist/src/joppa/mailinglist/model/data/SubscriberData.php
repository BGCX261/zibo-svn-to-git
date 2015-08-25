<?php

namespace joppa\mailinglist\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container for a mailinglist subscriber
 */
class SubscriberData extends Data {

	/**
	 * The email address of the subscriber
	 * @var string
	 */
	public $email;

	/**
	 * Gets the unsubscribe key
	 * @return string
	 */
	public function getUnsubscribeKey() {
		return md5($this->id);
	}

}