<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \Countable;
use \DOMDocument;
use \InvalidArgumentException;

/**
 * Abstract implementation of a ItemId collection element
 */
abstract class NonEmptyArrayOfBaseItemIds implements Countable {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the ItemId obejcts of this collection
     * @var array
     */
    protected $itemIds;

    /**
     * Constructs a new ItemIds element
     * @param string $name Name for the XML element of this type
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->itemIds = array();
    }

    /**
     * Adds a ItemId to the collection
     * @param ItemId $itemId
     */
    public function addItemId(ItemId $itemId) {
        $this->itemIds[] = $itemId;
    }

    /**
     * Implementation of Countable::count()
     * @return int
     */
    public function count() {
        return count($this->itemIds);
    }

    /**
     * Gets the XML of the provided element
     * @param NonEmptyArrayOfBaseItemIds $itemIds
     */
    public static function toXml(NonEmptyArrayOfBaseItemIds $itemIds) {
        $dom = $itemIds->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $baseFolderIds = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->itemIds as $item) {
            $doc = $item->toDom();
            $xmlItem = $dom->importNode($doc->documentElement, true);
            $baseFolderIds->appendChild($xmlItem);
        }

        $dom->appendChild($baseFolderIds);

        return $dom;
    }

}