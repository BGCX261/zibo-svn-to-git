<?php

namespace joppa\advertising\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a advertisement block
 */
class AdvertisementBlockData extends Data {

    /**
     * Name of this block
     * @var string
     */
    public $name;

    /**
     * Array with the advertisements for this block
     * @var array
     */
    public $advertisements;

}