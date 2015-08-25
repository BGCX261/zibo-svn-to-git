<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The FieldURI element identifies frequently referenced properties by URI.
 */
abstract class PathToUnindexedField {

    /**
     * Name of the field URI attribute
     * @var string
     */
    const ATTRIBUTE_FIELD_URI = 'FieldURI';

    /**
     * Name for the element of this type
     * @var string
     */
    private $name;

    /**
     * Identifies the URI of the property.
     * @var string
     */
    public $FieldURI;

    /**
     * Constructs a new path
     * @param string $name Name for the element of this type
     * @param string $fieldURI
     * @return null
     */
    public function __construct($name, $fieldURI) {
        $this->name = $name;

        $this->setFieldURI($fieldURI);
    }

    /**
     * Sets the field URI
     * @param string $fieldURI
     * @return null
     */
    public function setFieldURI($fieldURI) {
        $this->FieldURI = $fieldURI;
    }

    /**
     * Gets the DOM document of this type
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $path = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);
        $path->setAttribute(self::ATTRIBUTE_FIELD_URI, $this->FieldURI);

        $dom->appendChild($path);

        return $dom;
    }

}