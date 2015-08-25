<?php

namespace joppa\view\widget;

use joppa\form\widget\TitleWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of the title widget
 */
class TitleWidgetPropertiesView extends SmartyView {

    /**
     * Construct this view
     * @param joppa\form\widget\TitleWidgetPropertiesForm $form
     */
	public function __construct(TitleWidgetPropertiesForm $form) {
		parent::__construct('joppa/widget/title/properties');
		$this->set('form', $form);
	}

}