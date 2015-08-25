<?php

namespace joppa\security\view;

use joppa\security\form\ResetPasswordForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View to show the password reset widget
 */
class ResetPasswordView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/password/index';

	/**
	 * Constructs a new password reset view
	 * @param joppa\security\form\ResetPasswordForm $form Form of the view
	 * @return null
	 */
	public function __construct(ResetPasswordForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}