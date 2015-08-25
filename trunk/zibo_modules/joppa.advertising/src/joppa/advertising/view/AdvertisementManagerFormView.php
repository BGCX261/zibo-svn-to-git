<?php

namespace joppa\advertising\view;

use joppa\advertising\model\data\AdvertisementData;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\scaffold\form\ScaffoldForm;

/**
 * Form view for a advertisement
 */
class AdvertisementManagerFormView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/advertising/advertisement.form';

	/**
	 * Constructs a new form view
	 * @param zibo\orm\scaffold\form\ScaffoldForm $form
	 * @param string $title
	 * @param joppa\advertising\model\data\AdvertisementData $advertisement
	 * @return null
	 */
	public function __construct(ScaffoldForm $form, $title, AdvertisementData $advertisement = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
		$this->set('title', $title);
		$this->set('advertisement', $advertisement);
	}

}

