<?php

namespace joppa\view\widget;

use joppa\form\widget\BreadcrumbsWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of the breadcrumbs widget
 */
class BreadcrumbsWidgetPropertiesView extends SmartyView {

    /**
     * Construct this view
     * @param joppa\form\widget\BreadcrumbsWidgetPropertiesForm $form
     * @return null
     */
	public function __construct(BreadcrumbsWidgetPropertiesForm $form) {
		parent::__construct('joppa/widget/breadcrumbs/properties');
		$this->set('form', $form);
	}

}