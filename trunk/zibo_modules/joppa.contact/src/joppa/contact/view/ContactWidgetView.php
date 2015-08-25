<?php

namespace joppa\contact\view;

use joppa\contact\form\ContactWidgetForm;

use zibo\core\View;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the contact form widget
 */
class ContactWidgetView extends SmartyView {

	/**
	 * Path to the template of the view
	 * @var string
	 */
	const TEMPLATE = 'joppa/contact/contact';

	/**
	 * Construct a new view
	 * @param joppa\widget\form\ContactWidgetForm $form
	 * @param zibo\core\View $captchaView
	 * @return null
	 */
	public function __construct(ContactWidgetForm $form, View $captchaView) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);

		$this->setSubview('captcha', $captchaView);
	}

}