<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The PhysicalAddresses element contains a collection of physical addresses that are associated with a contact.
 */
class PhysicalAddressDictionary {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'PhysicalAddresses';

    /**
     * Entries of this dictionary
     * @var array
     */
    private $entries;

    /**
     * Construct a new PhysicialAddresses element
     * @return null
     */
    public function __construct() {
        $this->entries = array();
    }

    /**
     * Adds a entry to this dictionary
     * @param PhysicalAddressDictionaryEntry $entry
     * @return null
     */
    public function addEntry(PhysicalAddressDictionaryEntry $entry) {
        $this->entries[] = $entry;
    }

    /**
     * Gets the XML of the provided physical address dictionary
     * @param PhysicalAddressDictionary $dictionary
     * @return string
     */
    public static function toXml(PhysicalAddressDictionary $dictionary) {
        $dom = $dictionary->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $entries = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);

        foreach ($this->entries as $entry) {
            $entryDom = $entry->toDom();
            $entryElement = $dom->importNode($entryDom->documentElement, true);
            $entries->appendChild($entryElement);
        }

        $dom->appendChild($entries);

        return $dom;
    }

}