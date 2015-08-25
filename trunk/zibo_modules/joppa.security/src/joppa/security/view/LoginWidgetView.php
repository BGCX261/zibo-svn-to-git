<?php

namespace joppa\security\view;

use zibo\admin\form\AuthenticationForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the login widget
 */
class LoginWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/login/index';

	/**
	 * Constructs a new login widget view
	 * @param zibo\admin\form\AuthenticationForm $form
	 * @return null
	 */
	public function __construct(AuthenticationForm $form = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}