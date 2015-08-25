<?php

namespace joppa\security\view;

use joppa\security\form\PasswordWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the properties of the password reset widget
 */
class PasswordWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/password/properties';

	/**
	 * Constructs a new properties view
	 * @param joppa\security\form\PasswordWidgetPropertiesForm $form The password reset form
	 * @return null
	 */
	public function __construct(PasswordWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}