<?php

namespace joppa\mailinglist\controller;

use joppa\mailinglist\form\SubscribeForm;
use joppa\mailinglist\form\UnsubscribeForm;
use joppa\mailinglist\model\SubscriberModel;
use joppa\mailinglist\view\MailinglistWidgetView;

use zibo\library\validation\exception\ValidationException;
use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to subscribe and unsubscribe from the mailinglist
 */
class MailinglistWidget extends AbstractWidget {

	/**
	 * Name of the unsubscribe action
	 * @var string
	 */
	const ACTION_UNSUBSCRIBE = 'unsubscribe';

	/**
	 * Path to the icon of this widget
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/mailinglist.png';

	/**
	 * Translatio key for the name of this widget
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.mailinglist.widget.name';

	/**
	 * Translation key for the information message when subscribed
	 * @var string
	 */
	const TRANSLATION_SUBSCRIBED = 'joppa.mailinglist.message.email.subscribed';

	/**
	 * Translation key for the information message when unsubscribed
	 * @var string
	 */
	const TRANSLATION_UNSUBSCRIBED = 'joppa.mailinglist.message.email.unsubscribed';

	/**
	 * Translation key for the error message when the unsubscribe key is incorrect
	 * @var string
	 */
	const TRANSLATION_KEY_INCORRECT = 'joppa.mailinglist.error.key.incorrect';

	/**
	 * Translation key for the error message when a email address is not subscribed
	 * @var string
	 */
	const TRANSLATION_NOT_FOUND = 'joppa.mailinglist.error.email.not.found';

	/**
	 * Translation key for the information message that a unsubscribe request has been sent
	 * @var string
	 */
	const TRANSLATION_CONFIRMATION_MAIL = 'joppa.mailinglist.message.unsubscribe.email';

	/**
	 * Hook with the ORM module
	 * @var string
	 */
	public $useModels = SubscriberModel::NAME;

	/**
	 * Constucts a new instance of the mailinglist widget
	 * @return null
	 */
	public function __construct() {
		parent::__construct(self::TRANSLATION_NAME, self::ICON);
	}

	/**
	 * Gets the names of the possible request parameters of this widget
	 * @return array
	 */
	public function getRequestParameters() {
		return array(self::ACTION_UNSUBSCRIBE);
	}

	/**
	 * Action to show the subscribe and unsubscribe form
	 * @return null
	 */
	public function indexAction() {
		$basePath = $this->request->getBasePath();

		$subscribeForm = new SubscribeForm($basePath);
		if ($subscribeForm->isSubmitted()) {
			try {
				$subscribeForm->validate();

				$subscriber = $this->models[SubscriberModel::NAME]->create();
				$subscriber->email = $subscribeForm->getEmail();

				$this->models[SubscriberModel::NAME]->save($subscriber);

				$this->addInformation(self::TRANSLATION_SUBSCRIBED, array('email' => $subscriber->email));
				$this->response->setRedirect($basePath);
				return;
			} catch (ValidationException $e) {
				$subscribeForm->setValidationException($e);
			}
		}

		$unsubscribeForm = new UnsubscribeForm($basePath);
		if ($unsubscribeForm->isSubmitted()) {
			try {
				$unsubscribeForm->validate();

				$email = $unsubscribeForm->getEmail();

				$this->response->setRedirect($basePath . '/' . self::ACTION_UNSUBSCRIBE . '/' . $email);
				return;
			} catch (ValidationException $e) {
				$unsubscribeForm->setValidationException($e);
			}
		}

		$view = new MailinglistWidgetView($subscribeForm, $unsubscribeForm);
		$this->response->setView($view);
	}

	/**
	 * Action to request or execute a unsubscribe from the mailinglist
	 * @param string $email Email address to unsubscribe
	 * @param string $key If provided, the subscriber will be unsubscribed, else a unsubscribe mail will be sent
	 * @return null
	 */
	public function unsubscribeAction($email, $key = null) {
		$this->response->setRedirect($this->request->getBasePath());

		$subscriber = $this->models[SubscriberModel::NAME]->findFirstBy('email', $email);
		if (!$subscriber) {
			$this->addInformation(self::TRANSLATION_NOT_FOUND, array('email' => $email));
			return;
		}

		if ($key) {
			if ($key == $subscriber->getUnsubscribeKey()) {
				$this->models[SubscriberModel::NAME]->delete($subscriber);
				$this->addInformation(self::TRANSLATION_UNSUBSCRIBED, array('email' => $subscriber->email));
			} else {
				$this->addError(self::TRANSLATION_KEY_INCORRECT, array('email' => $subscriber->email));
			}
		} else {
			$this->models[SubscriberModel::NAME]->requestUnsubscribe($subscriber);
			$this->addInformation(self::TRANSLATION_CONFIRMATION_MAIL, array('email' => $subscriber->email));
		}
	}

}