<?php

namespace joppa\view\widget;

use joppa\form\widget\RedirectWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of a redirect widget
 */
class RedirectWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/widget/redirect/properties';

    /**
     * Construct this view
     * @param joppa\form\widget\RedirectWidgetPropertiesForm $form
     * @return null
     */
    public function __construct(RedirectWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}