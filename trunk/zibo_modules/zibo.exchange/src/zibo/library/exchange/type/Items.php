<?php

namespace zibo\library\exchange\type;

/**
 * The Items element contains a set of items to create.
 */
class Items extends NonEmptyArrayOfAllItems {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Items';

    /**
     * Constructs a new Items element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}