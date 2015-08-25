<?php

namespace zibo\core\router\io;

/**
 * Cached XML implementation of the RouterIO
 */
class CachedXmlRouterIO extends CachedRouterIO {

    /**
     * Constructs a new cached XML router IO
     * @return null
     */
    public function __construct() {
        parent::__construct(new XmlRouterIO());
    }

}