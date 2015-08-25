<?php

namespace joppa\security\view;

use joppa\security\form\NewPasswordForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View to show the password form
 */
class NewPasswordView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/password/new';

	/**
	 * Constructs a new password reset view
	 * @param joppa\security\form\NewPasswordForm $form Form of the view
	 * @return null
	 */
	public function __construct(NewPasswordForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}