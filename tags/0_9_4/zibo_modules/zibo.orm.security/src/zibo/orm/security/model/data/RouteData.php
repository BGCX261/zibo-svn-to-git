<?php

namespace zibo\orm\security\model\data;

use zibo\library\orm\model\data\Data;

/**
 * Route data container
 */
class RouteData extends Data {

    /**
     * Route
     * @var string
     */
    public $route;

    /**
     * Flag to see if this route is globally denied
     * @var boolean
     */
    public $isDenied;

}