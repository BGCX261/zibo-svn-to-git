<?php

namespace joppa\mailinglist\model\data;

use joppa\model\NodeModel;

use zibo\core\Zibo;

use zibo\library\mail\Message;
use zibo\library\orm\model\data\Data;
use zibo\library\orm\ModelManager;

use zibo\ZiboException;

/**
 * Data container for a mailinglist message
 */
class MessageData extends Data {

	/**
	 * The subject of the message
	 * @var string
	 */
	public $subject;

	/**
	 * The message
	 * @var string
	 */
	public $message;

	/**
	 * The recipients
	 * @var array
	 */
	public $recipients;

	/**
	 * The errors while sending the message
	 * @var array
	 */
	private $sendErrors;

	/**
	 * Gets the errors which occured while sending the message
	 * @return array Array with the email address as key and the exception as value
	 */
	public function getSendErrors() {
		return $this->sendErrors;
	}

	/**
	 * Sends out the message to all the recipients
	 * @return boolean True on success, false when errors occured
	 * @see getSendErrors
	 */
	public function send() {
		$recipients = array();
		foreach ($this->recipients as $subscriber) {
			$recipients[] = $subscriber->email;
		}

		$mail = $this->createMail();

		$this->sendErrors = array();

		$success = true;
		foreach ($recipients as $recipient) {
			try {
				$mail->setTo($recipient);
				$mail->send();
			} catch (ZiboException $exception) {
				$this->sendErrors[$recipient] = $exception;
				$success = false;
			}
		}

		return $success;
	}

	/**
	 * Creates the mail message
	 * @return zibo\library\mail\Message
	 */
	protected function createMail() {
		$mail = new Message();

		$mail->setSubject($this->subject);
		$mail->setMessage($this->parseMessage($this->message));

		$mail->setIsHtmlMessage(true);
		$mail->removePart(MailMessage::PART_ALTERNATIVE);

		return $mail;
	}

	/**
	 * Parses the message, adds the unsubscribe footer to it
	 * @param string $message The unparsed message
	 * @return string The parsed message
	 */
	protected function parseMessage($message) {
		$nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
		$node = $nodeModel->getNodesForWidget('joppa', 'mailinglist', 1);
		if ($node) {
			$baseUrl = Zibo::getInstance()->getRequest()->getBaseUrl();
			$url = $baseUrl . $node->getRoute();
			$message .= "<p style=\"margin-top: 18px; font-size: 75%;\">---<br />Unsubscribe: <a href=\"" . $url . '">' . $url . '</a></p>';
		}

		return $message;
	}

}