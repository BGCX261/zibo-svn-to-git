<?php

namespace joppa\security\view;

use joppa\security\form\RegisterForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View to show the registration form
 */
class RegisterView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/register/index';

	/**
	 * Constructs a new register view
	 * @param joppa\security\form\RegisterForm $form Form of the view
	 * @return null
	 */
	public function __construct(RegisterForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}