<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Parser for the global tag
 */
class GlobalTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'global';

    /**
     * Construct a new tag parser
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}