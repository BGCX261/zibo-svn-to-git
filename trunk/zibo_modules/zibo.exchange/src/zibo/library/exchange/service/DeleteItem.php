<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\ItemIds;

/**
 * The DeleteItem element defines a request to delete an item from a mailbox in the Exchange store.
 */
class DeleteItem {

    /**
     * An item is permanently removed from the store.
     * @var string
     */
    const TYPE_HARD_DELETE = 'HardDelete';

    /**
     * An item is moved to the dumpster if the dumpster is enabled.
     * @var string
     */
    const TYPE_SOFT_DELETE = 'SoftDelete';

    /**
     * An item is moved to the Deleted Items folder.
     * @var string
     */
    const TYPE_MOVE_TO_DELETED_ITEMS = 'MoveToDeletedItems';

    /**
     * Contains an array of items, occurrence items, and recurring master items to delete from a mailbox in the Exchange store. The DeleteItem Operation can be performed on any item type.
     * @var zibo\library\exchange\type\ItemIds ItemIds
     */
    public $ItemIds;

    /**
     * Describes how an item is deleted
     * @var string
     */
    public $DeleteType;

    /**
     * Constructs a new DeleteItem element
     * @param zibo\library\exchange\type\ItemIds $folderIds
     * @param string $deleteType Describes how an item is deleted. This attribute is required.
     * @return null
     * @throws InvalidArgumentException when the provided delete type is not null or not a valid delete type
     */
    public function __construct(ItemIds $itemIds, $deleteType = null) {
        $this->setDeleteType($deleteType);
        $this->ItemIds = $itemIds;
    }

    /**
     * Sets the delete type
     * @param string $deleteType Describes how an item is deleted. This attribute is required.
     * @return null
     * @throws InvalidArgumentException when the provided delete type is not null or not a valid delete type
     */
    public function setDeleteType($deleteType) {
        if ($deleteType === null) {
            $deleteType = self::TYPE_MOVE_TO_DELETED_ITEMS;
        } else if ($deleteType != self::TYPE_HARD_DELETE && $deleteType != self::TYPE_SOFT_DELETE && $deleteType != self::TYPE_MOVE_TO_DELETED_ITEMS) {
            throw new InvalidArgumentException('Provided delete type is not valid');
        }

        $this->DeleteType = $deleteType;
    }

}