<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \Countable;
use \DOMDocument;

/**
 * Abstract implementation of a item collection element
 */
abstract class NonEmptyArrayOfAllItems implements Countable {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the items for this collection
     * @var array
     */
    protected $items;

    /**
     * Constructs a new Items element
     * @param string $name Name for the XML element
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->items = array();
    }

    /**
     * Adds an Item to this collection
     * @param Item $item
     */
    public function addItem(Item $item) {
        $this->items[] = $item;
    }

    /**
     * Implementation of Countable::count()
     * @return int
     */
    public function count() {
        return count($this->items);
    }

    /**
     * Gets the XML of the provided Items element
     * @param NonEmptyArrayOfAlItems $items
     * @return string
     */
    public static function toXml(NonEmptyArrayOfAllItems $items) {
        $dom = $items->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $items = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->items as $item) {
            $itemDom = $item->toDom();
            $itemElement = $dom->importNode($itemDom->documentElement, true);
            $items->appendChild($itemElement);
        }

        $dom->appendChild($items);

        return $dom;
    }

}
