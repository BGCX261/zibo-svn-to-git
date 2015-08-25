<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \Countable;
use \DOMDocument;

/**
 * Element which acts as a collection of ItemChangeDescription elements
 */
abstract class NonEmptyArrayOfItemChangeDescriptions implements Countable {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the ItemChangeDescriptions
     * @var array
     */
    protected $itemChangeDescriptions;

    /**
     * Constructs a new ItemChangeDescriptions element
     * @param string $name Name for the XML element of the type
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->itemChangeDescriptions = array();
    }

    /**
     * Adds a ItemChangeDescription element to this collection
     * @param ItemChangeDescription $itemChangeDescription
     * @return null
     */
    public function addItemChangeDescription(ItemChangeDescription $itemChangeDescription) {
        $this->itemChangeDescriptions[] = $itemChangeDescription;
    }

    /**
     * Implementation of Countable::count()
     * @return int
     */
    public function count() {
        return count($this->itemChangeDescriptions);
    }

    /**
     * Gets the XML for the provided ItemChangeDescriptions
     * @param NonEmptyArrayOfItemChangeDescriptions $itemChangeDescriptions
     * @return string
     */
    public static function toXml(NonEmptyArrayOfItemChangeDescriptions $itemChangeDescriptions) {
        $dom = $itemChangeDescriptions->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $itemChangeDescriptions = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->itemChangeDescriptions as $itemChangeDescription) {
            $itemChangeDescriptionDom = $itemChangeDescription->toDom();
            $itemChangeDescriptionElement = $dom->importNode($itemChangeDescriptionDom->documentElement, true);
            $itemChangeDescriptions->appendChild($itemChangeDescriptionElement);
        }

        $dom->appendChild($itemChangeDescriptions);

        return $dom;
    }

}
