<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The PhoneNumbers element represents a collection of telephone numbers for a contact.
 */
class PhoneNumberDictionary {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'PhoneNumbers';

    /**
     * Array with the phone number entries
     * @var array
     */
    private $entries;

    /**
     * Constructs a ne PhoneNumbers element
     * @return null
     */
    public function __construct() {
        $this->entries = array();
    }

    /**
     * Adds a phone number entry to this collection
     * @param PhoneNumberDictionaryEntry $entry
     * @return null
     */
    public function addEntry(PhoneNumberDictionaryEntry $entry) {
        $this->entries[] = $entry;
    }

    /**
     * Gets the XML of the PhoneNumbers element
     * @param PhoneNumberDictionary $dictionary
     * @return string
     */
    public static function toXml(PhoneNumberDictionary $dictionary) {
        $dom = $dictionary->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document for this dictionary
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