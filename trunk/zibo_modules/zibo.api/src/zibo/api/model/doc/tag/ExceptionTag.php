<?php

namespace zibo\api\model\doc\tag;

/**
 * Parser for the exception tag
 */
class ExceptionTag extends ThrowsTag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'exception';

    /**
     * Construct a new tag parser
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}