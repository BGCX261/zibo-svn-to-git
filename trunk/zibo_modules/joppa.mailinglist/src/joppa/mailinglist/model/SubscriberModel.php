<?php

namespace joppa\mailinglist\model;

use joppa\mailinglist\model\data\SubscriberData;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\mail\Message;
use zibo\library\orm\model\ExtendedModel;
use zibo\library\validation\ValidationError;
use zibo\library\validation\ValidationException;

use zibo\ZiboException;

/**
 * Model of the mailinglist subscribers
 */
class SubscriberModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'MailinglistSubscriber';

	/**
	 * Configuration key for the mailinglist sender
	 * @var string
	 */
	const CONFIG_SENDER = 'mailinglist.sender';

	/**
	 * Configuration key for the path to unsubscribe from the mailinglist
	 * @var string
	 */
	const CONFIG_UNSUBSCRIBE_PATH = 'mailinglist.path.unsubscribe';

	/**
	 * Translation key for the email already subscribed error
	 * @var string
	 */
	const TRANSLATION_ERROR_SUBSCRIBED = 'joppa.mailinglist.error.email.subscribed';

	/**
	 * Translation key for the subject of the unsubscribe message
	 * @var string
	 */
	const TRANSLATION_UNSUBSCRIBE_SUBJECT = 'joppa.mailinglist.unsubscribe.subject';

	/**
	 * Translation key for the message of the unsubscribe message
	 * @var string
	 */
	const TRANSLATION_UNSUBSCRIBE_MESSAGE = 'joppa.mailinglist.unsubscribe.message';

	/**
	 * Translation key for the footer of the unsubscribe message
	 * @var string
	 */
	const TRANSLATION_UNSUBSCRIBE_FOOTER = 'joppa.mailinglist.unsubscribe.footer';

	/**
	 * Gets all the subscribers
	 * @return array Array with SubscriberData objects
	 */
	public function getSubscribers() {
		$query = $this->createQuery();
		return $query->query();
	}

	/**
	 * Imports a list of email addresses
	 * @param array $emailAddresses Array with email addresses to import
	 * @return array Array with exceptions which occured while importing the provided email addresses
	 */
	public function importSubscribers(array $emailAddresses) {
		$exceptions = array();

		foreach ($emailAddresses as $emailAddress) {
			try {
				$data = $this->create();
				$data->email = $emailAddress;
				$this->save($data);
			} catch (ZiboException $e) {
				$exceptions[$emailAddress] = $e;
			}
		}

		return $exceptions;
	}

	/**
	 * Validates the provided subscriber
	 * @param joppa\mailinglist\model\data\SubscriberData $data The subscriber to validate
	 * @return null
	 * @throws zibo\library\validation\exception\ValidationException when a validation exception occurs
	 */
	public function validate($data) {
		try {
			parent::validate($data);
			$exception = new ValidationException();
		} catch (ValidationException $e) {
			$exception = $e;
		}

		$query = $this->createQuery(false);
		$query->addCondition('{email} = %1%', $data->email);

		if ($data->id) {
			$query->addCondition('{id} <> %1%', $data->id);
		}

		if ($query->count()) {
			$error = new ValidationError(self::TRANSLATION_ERROR_SUBSCRIBED, '%email% is already subscribed', array('email' => $data->email));
			$exception->addErrors('email', array($error));
		}

		if ($exception->hasErrors()) {
			throw $exception;
		}
	}

	/**
	 * Sends a mail to the provided subscriber to request a unsubscribe
	 * @param joppa\mailinglist\model\data\SubscriberData $subscriber The subscriber who wants to unsubscribe
	 * @return null
	 */
	public function requestUnsubscribe(SubscriberData $subscriber) {
		$translator = I18n::getInstance()->getTranslator();
		$zibo = Zibo::getInstance();

		$sender = $zibo->getConfigValue(self::CONFIG_SENDER);

		$website = $zibo->getRequest()->getBaseUrl();
		$unsubscribeUrl = $website . $zibo->getConfigValue(self::CONFIG_UNSUBSCRIBE_PATH);
		$confirmUrl = $unsubscribeUrl . '/' . $subscriber->email . '/' . $subscriber->getUnsubscribeKey();

		$parameters = array(
			'email' => $subscriber->email,
			'confirmUrl' => $confirmUrl,
			'unsubscribeUrl' => $unsubscribeUrl,
			'website' => $website,
		);

		$subject = $translator->translate(self::TRANSLATION_UNSUBSCRIBE_SUBJECT, $parameters);
		$message = $translator->translate(self::TRANSLATION_UNSUBSCRIBE_MESSAGE, $parameters);

		$mail = new Message();
		if ($sender) {
			$mail->setFrom($sender);
		}
		$mail->setTo($subscriber->email);
		$mail->setSubject($subject);
		$mail->setMessage($message);
		$mail->setIsHtmlMessage(true);
		$mail->send();
	}

}