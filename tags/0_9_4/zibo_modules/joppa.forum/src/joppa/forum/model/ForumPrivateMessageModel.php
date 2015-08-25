<?php

namespace joppa\forum\model;

use zibo\library\orm\model\ExtendedModel;

/**
 * Model of the private messages
 */
class ForumPrivateMessageModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ForumPrivateMessage';

	/**
	 * Sends a new message
	 * @param integer $idSender Id of the sender user
	 * @param integer $idRecipient Id of the recipient user
	 * @param string $subject Subject of the message
	 * @param string $message Content of the message
	 * @return null
	 * @throw zibo\library\validation\exception\ValidationException when invalid data has been provided
	 */
	public function sendMessage($idSender, $idRecipient, $subject, $message) {
		$pm = $this->createData();
		$pm->sender = $idSender;
		$pm->recipientId = $idRecipient;
		$pm->subject = $subject;
		$pm->message = $message;

		$this->save($pm);
	}

	/**
	 * Count new messages for a profile
	 * @param integer $idProfile Id of the profile
	 * @return integer Number of new messages
	 */
	public function countNewMessages($idProfile) {
		$query = $this->createQuery(0);
		$query->addCondition('{recipient} = %1%', $userId);
		$query->addCondition('{isNew} = %1%', true);
		return $query->count();
	}

	/**
	 * Gets the inbox for a profile
	 * @param integer $idProfile Id of the profile
	 * @return array Array with ForumPrivateMessageData objects
	 */
	public function getInbox($idProfile) {
		$query = $this->createQuery();
		$query->addCondition('{recipient} = %1%', $idProfile);
		$query->addCondition('{isDeleted} <> %1%', false);
		$query->addOrderBy('{dateAdded} DESC');
		return $query->query();
	}

	/**
	 * Gets the sent items of a profile
	 * @param integer $idProfile Id of the profile
	 * @return array Array with ForumPrivateMessageData objects
	 */
	public function getSentItems($idProfile) {
        $query = $this->createQuery();
        $query->addCondition('{sender} = %1%', $idProfile);
        $query->addOrderBy('{dateAdded} DESC');
        return $query->query();
	}

	/**
	 * Gets the deleted items of a profile
	 * @param integer $idProfile Id of the profile
	 * @return array Array with ForumPrivateMessageData objects
	 */
	public function getDeletedItems($idProfile) {
        $query = $this->createQuery();
        $query->addCondition('{recipient} = %1%', $idProfile);
        $query->addCondition('{isDeleted} = %1%', true);
        $query->addOrderBy('{dateAdded} DESC');
        return $query->query();
	}

}