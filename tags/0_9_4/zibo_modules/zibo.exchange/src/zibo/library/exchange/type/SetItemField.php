<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The SetItemField element represents an update to a single property of an item in an UpdateItem Operation.
 */
class SetItemField extends ItemChangeDescription {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'SetItemField';

    /**
     * Item to set fields for
     * @var Item
     */
    private $item;

    /**
     * Constructs a new SetItemField element
     * @param FieldURI $fieldURI URI of the field to save
     * @param Item $item Item containing the field
     * @return null
     */
    public function __construct(PathToUnindexedField $fieldURI, Item $item) {
        parent::__construct(self::NAME, $fieldURI);

        $this->item = $item;
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $changeDescription = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        $fieldURIDom = $this->FieldURI->toDom();
        $fieldURIElement = $dom->importNode($fieldURIDom->documentElement, true);
        $changeDescription->appendChild($fieldURIElement);

        $itemDom = $this->item->toDom();
        $itemElement = $dom->importNode($itemDom->documentElement, true);
        $changeDescription->appendChild($itemElement);

        $dom->appendChild($changeDescription);

        return $dom;
    }

}