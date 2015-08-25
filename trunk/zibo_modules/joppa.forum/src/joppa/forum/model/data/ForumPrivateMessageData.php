<?php

namespace joppa\forum\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a private message. A private message (pm) is a message to a specific user only.
 * This is for sharing private information with other forum users.
 */
class ForumPrivateMessageData extends Data {

	/**
	 * The sender of this message
	 * @var integer|UserData
	 */
	public $sender;

	/**
	 * The recipient of this message
	 * @var integer|UserData
	 */
	public $recipient;

	/**
	 * Flag to see if this message has been read
	 * @var boolean
	 */
	public $isNew;

	/**
	 * Flag to see if this message has been deleted
	 * @var boolean
	 */
	public $isDeleted;

	/**
	 * The subject of this pm
	 * @var string
	 */
	public $subject;

	/**
	 * The message of this pm
	 * @var string
	 */
	public $message;

	/**
	 * Timestamp when this message was created
	 * @var integer
	 */
	public $dateAdded;

}