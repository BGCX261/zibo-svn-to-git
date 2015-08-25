<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\DefaultShapeNames;
use zibo\library\exchange\type\ItemIds;
use zibo\library\exchange\type\ItemResponseShape;

/**
 * The GetItem operation is used to get contact items from the Exchange store.
 */
class GetItem {

    /**
     * Identifies the item properties and content to include in the response.
     * @var zibo\library\exchange\type\ItemResponseShape
     */
    public $ItemShape;

    /**
     * Contains the unique identities of items, occurrence items, and recurring master items that are used to get items from the Exchange store.
     * These items represent contacts, tasks, messages, calendar items, meeting requests, and other valid items in a mailbox.
     * @var zibo\library\exchange\type\ItemIds
     */
    public $ItemIds;

    /**
     * Constructs a new FindFolder element
     * @param zibo\library\exchange\type\ItemIds $itemIds Contains the unique identities of items
     * @param zibo\library\exchange\type\ItemResponseShape $itemShape Identifies the item properties and content to include in the response.
     * @return null
     * @throws InvalidArgumentException when the provided traversal is not null or not a valid traversal string
     */
    public function __construct(ItemIds $itemIds, ItemResponseShape $itemShape = null) {
        $this->setTraversal($traversal);

        if ($itemShape === null) {
            $itemShape = new ItemResponseShape(DefaultShapeNames::SHAPE_ALL_PROPERTIES);
        }

        $this->ItemShape = $itemShape;
        $this->ItemIds = $itemIds;
    }

}