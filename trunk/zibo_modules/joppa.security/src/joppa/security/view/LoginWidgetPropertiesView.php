<?php

namespace joppa\security\view;

use joppa\security\form\LoginWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the properties of the login widget
 */
class LoginWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/login/properties';

	/**
	 * Constructs a new properties view for the login widget
	 * @param joppa\security\form\LoginWidgetPropertiesForm $form The properties form
	 * @return null
	 */
	public function __construct(LoginWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}