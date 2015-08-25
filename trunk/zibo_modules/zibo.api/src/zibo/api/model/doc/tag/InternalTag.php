<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Parser for the internal tag
 */
class InternalTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'internal';

    /**
     * Construct a new tag parser
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}