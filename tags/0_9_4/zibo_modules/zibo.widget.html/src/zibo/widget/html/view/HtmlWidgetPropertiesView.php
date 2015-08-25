<?php

namespace zibo\widget\html\view;

use zibo\library\smarty\view\SmartyView;

use zibo\widget\html\form\HtmlWidgetPropertiesForm;

/**
 * Properties view for the HTML widget
 */
class HtmlWidgetPropertiesView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'widget/html/properties';

    /**
     * Constructs a new properties view for the HTML widget
     * @param zibo\widget\html\form\HtmlWidgetPropertiesForm $form
     * @return null
     */
	public function __construct(HtmlWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}