<?php

namespace joppa\advertising\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of an advertisement
 */
class AdvertisementData extends Data {

	/**
	 * Name of the advertisement
	 * @var string
	 */
    public $name;

    /**
     * URL to redirect to when the advertisement gets clicked
     * @var string
     */
    public $website;

    /**
     * Path to the image of the advertisement
     * @var string
     */
    public $image;

    /**
     * Internal click URL
     * @var string
     */
    public $url;

    /**
     * Start date of this advertisement
     * @var int
     */
    public $dateStart;

    /**
     * Stop date of this advertisement
     * @var int
     */
    public $dateStop;

    /**
     * Array with the blocks where this advertisement should be shown
     * @var array
     */
    public $blocks;

    /**
     * Times this advertisement has been clicked
     * @var int
     */
    public $clicks;

    /**
     * Gets a string representation of this object
     * @return string
     */
    public function __toString() {
    	return $this->id . ' - ' . $this->name . ' - ' . $this->image;
    }

}