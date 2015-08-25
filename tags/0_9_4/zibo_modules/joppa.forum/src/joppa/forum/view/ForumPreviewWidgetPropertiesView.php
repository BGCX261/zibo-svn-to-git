<?php

namespace joppa\forum\view;

use joppa\forum\form\ForumPreviewWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of a search form widget
 */
class ForumPreviewWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/properties.preview';

    /**
     * Construct this view
     * @param joppa\forum\form\ForumPreviewWidgetPropertiesForm $form
     * @return null
     */
    public function __construct(ForumPreviewWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}