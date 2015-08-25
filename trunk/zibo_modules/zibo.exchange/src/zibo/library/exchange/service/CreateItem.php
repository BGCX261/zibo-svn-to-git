<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\SavedItemFolderId;
use zibo\library\exchange\type\Items;

/**
 * The CreateItem element defines a request to create an item in the Exchange store.
 */
class CreateItem {

    /**
     * Identifies the target folder where a new item can be created
     * @param zibo\library\exchange\type\SavedItemFolderId
     */
    public $SavedItemFolderId;

    /**
     * Contains an array of items to create in the folder that is identified by the SavedItemFolderId  element.
     * @param zibo\library\exchange\type\Items
     */
    public $Items;

    /**
     * Constructs a new CreateItem element
     * @param zibo\library\exchange\type\SavedItemFolderId $savedItemFolderId Identifies the target folder where a new item can be created
     * @param zibo\library\exchange\type\Items $items Contains an array of items to create in the folder that is identified by the SavedItemFolderId  element.
     * @return null
     */
    public function __construct(SavedItemFolderId $savedItemFolderId, Items $items) {
        $this->SavedItemFolderId = $savedItemFolderId;
        $this->Items = $items;
    }

}