<?php

namespace zibo\library\exchange\type;

/**
 * The ItemChanges element contains an array of ItemChange  elements that identify items and the updates to apply to the items.
 */
class ItemChanges extends NonEmptyArrayOfItemChanges {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'ItemChanges';

    /**
     * Construct a new ItemChanges element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}