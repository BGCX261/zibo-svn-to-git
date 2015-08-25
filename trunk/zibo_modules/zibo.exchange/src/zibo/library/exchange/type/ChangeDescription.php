<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * Abstract element to represent an update of a single property of an item in an UpdateItem Operation.
 */
abstract class ChangeDescription {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Identifies the field which needs to be changed
     * @var PathToUnindexedField
     */
    public $FieldURI;

    /**
     * Constructs a new ChangeDescription element
     * @param string $name Name for the XML element of this type
     * @param PathToUnindexedField $fieldURI Identifies the field which needs to be changed
     * @return null
     */
    public function __construct($name, PathToUnindexedField $fieldURI) {
        $this->name = $name;
        $this->FieldURI = $fieldURI;
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

        $dom->appendChild($changeDescription);

        return $dom;
    }

}