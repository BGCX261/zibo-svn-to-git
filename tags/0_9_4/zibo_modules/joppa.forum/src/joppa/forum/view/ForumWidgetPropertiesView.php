<?php

namespace joppa\forum\view;

use joppa\forum\form\ForumWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of a search form widget
 */
class ForumWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/properties';

    /**
     * Construct this view
     * @param joppa\forum\form\ForumWidgetPropertiesForm $form
     * @return null
     */
    public function __construct(ForumWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}