<?php

namespace zibo\library\exchange\type;

/**
 * The ItemIds element contains the unique identities of items, occurrence items, and recurring master items that are used to delete, send, get, move, or copy items in the Exchange store.
 */
class ItemIds extends NonEmptyArrayOfBaseItemIds {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'ItemIds';

    /**
     * Constructs a new ItemsIds element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}