<?php

namespace joppa\advertising\view;

use joppa\advertising\model\Advertisement;

use zibo\library\smarty\view\SmartyView;

/**
 * View to display an advertisement
 */
class AdvertisementWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/advertising/advertisement';

	/**
     * Constructs a new advertisement view
     * @param joppa\advertising\model\Advertisement $advertisement
     * @param int $width
     * @param int $height
     * @return null
	 */
	public function __construct(Advertisement $advertisement, $width, $height) {
		parent::__construct(self::TEMPLATE);

		$this->set('advertisement', $advertisement);
		$this->set('width', $width);
		$this->set('height', $height);
	}

}

