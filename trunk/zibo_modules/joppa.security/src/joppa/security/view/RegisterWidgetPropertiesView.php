<?php

namespace joppa\security\view;

use joppa\security\form\RegisterWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the properties of the register widget
 */
class RegisterWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/register/properties';

	/**
	 * Constructs a new properties view
	 * @param joppa\security\form\RegisterWidgetPropertiesForm $form The register properties form
	 * @return null
	 */
	public function __construct(RegisterWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}