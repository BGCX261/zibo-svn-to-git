<?php

namespace joppa\mailinglist\view;

use joppa\mailinglist\form\SubscribeForm;
use joppa\mailinglist\form\UnsubscribeForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the mailinglist widget
 */
class MailinglistWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/mailinglist/index';

	/**
	 * Constructs a new view for the mailinglist widget
	 * @param joppa\mailinglist\form\SubscribeForm $subscribeForm The subscribe form
	 * @param joppa\mailinglist\form\UnsubscribeForm $unsubscribeForm The unsubscribe form
	 * @return null
	 */
	public function __construct(SubscribeForm $subscribeForm, UnsubscribeForm $unsubscribeForm) {
		parent::__construct(self::TEMPLATE);

		$this->set('formSubscribe', $subscribeForm);
		$this->set('formUnsubscribe', $unsubscribeForm);
	}

}