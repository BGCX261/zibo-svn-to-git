<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \Countable;
use \DOMDocument;

/**
 * ItemChanges element contains an array of ItemChange  elements that identify items and the updates to apply to the items.
 */
abstract class NonEmptyArrayOfItemChanges implements Countable {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the item changes
     * @var unknown_type
     */
    private $itemChanges;

    /**
     * Constructs a new ItemChanges element
     * @param string $name Name for the XML element of this type
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->itemChanges = array();
    }

    /**
     * Adds a new ItemChange element to this collection
     * @param ItemChange $itemChange
     * @return null
     */
    public function addItemChange(ItemChange $itemChange) {
        $this->itemChanges[] = $itemChange;
    }

    /**
     * Implementation of Countable::count()
     * @return int
     */
    public function count() {
        return count($this->itemChanges);
    }

    /**
     * Gets the XML of the provided ItemChanges element
     * @param NonEmptyArrayOfItemChanges $itemChanges
     */
    public static function toXml(NonEmptyArrayOfItemChanges $itemChanges) {
        $dom = $itemChanges->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $itemChanges = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->itemChanges as $itemChange) {
            $itemChangeDom = $itemChange->toDom();
            $itemChangeElement = $dom->importNode($itemChangeDom->documentElement, true);
            $itemChanges->appendChild($itemChangeElement);
        }

        $dom->appendChild($itemChanges);

        return $dom;
    }

}
