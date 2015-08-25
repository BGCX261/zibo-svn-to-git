<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

class ItemChange {

    /**
     * Name of this type
     * @var unknown_type
     */
    const NAME = 'ItemChange';

    /**
     * Id of the item
     * @var ItemId
     */
    public $ItemId;

    /**
     *
     * @var unknown_type
     */
    public $Updates;

    /**
     * Constructs a new item change
     * @param ItemId $itemId
     */
    public function __construct(ItemId $itemId, NonEmptyArrayOfItemChangeDescriptions $updates) {
        $this->ItemId = $itemId;
        $this->Updates = $updates;
    }

    /**
     * Gets a DOM element of this folder
     * @return \DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $itemChange = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);

        $itemIdDom = $this->ItemId->toDom();
        $itemIdElement = $dom->importNode($itemIdDom->documentElement, true);
        $itemChange->appendChild($itemIdElement);

        $updatesDom = $this->Updates->toDom();
        $updatesElement = $dom->importNode($updatesDom->documentElement, true);
        $itemChange->appendChild($updatesElement);

        $dom->appendChild($itemChange);

        return $dom;
    }

}