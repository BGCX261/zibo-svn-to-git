<?php

namespace joppa\contact\view;

use joppa\contact\form\ContactWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View to show the properties form of the contact widget
 */
class ContactWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/contact/properties';

	/**
	 * Construct a new properties view
	 * @param joppa\widget\form\ContactWidgetPropertiesForm $form
	 * @return null
	 */
	public function __construct(ContactWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}