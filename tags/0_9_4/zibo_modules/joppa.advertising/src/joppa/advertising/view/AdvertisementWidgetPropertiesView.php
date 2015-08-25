<?php

namespace joppa\advertising\view;

use joppa\advertising\form\AdvertisementWidgetPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * Properties view for a advertisement widget
 */
class AdvertisementWidgetPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/advertising/properties';

	/**
	 * Constructs a new properties view
	 * @param joppa\advertising\form\AdvertisementWidgetPropertiesForm $form
	 * @return null
	 */
	public function __construct(AdvertisementWidgetPropertiesForm $form) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
	}

}

