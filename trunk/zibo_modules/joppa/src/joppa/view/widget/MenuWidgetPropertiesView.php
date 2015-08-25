<?php

namespace joppa\view\widget;

use joppa\form\widget\MenuWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of the menu widget
 */
class MenuWidgetPropertiesView extends SmartyView {

    /**
     * Construct this view
     * @param joppa\form\widget\MenuWidgetPropertiesForm $form
     */
	public function __construct(MenuWidgetPropertiesForm $form) {
		parent::__construct('joppa/widget/menu/properties');
		$this->set('form', $form);
	}

}