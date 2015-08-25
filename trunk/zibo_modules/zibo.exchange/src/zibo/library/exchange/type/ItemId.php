<?php

namespace zibo\library\exchange\type;

/**
 * The ItemId element contains the unique identifier and change key of an item in the Exchange store.
 */
class ItemId extends BaseId {

    /**
     * Name of this element
     * @var string
     */
    const NAME = 'ItemId';

    /**
     * Constructs a new ItemId element
     * @param $Id
     */
    public function __construct($id, $changeKey = null) {
        parent::__construct(self::NAME, $id, $changeKey);
    }

}
