<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The EmailAddresses element represents a collection of e-mail addresses for a contact.
 */
class EmailAddressDictionary {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'EmailAddresses';

    /**
     * Array with the entries for this element
     * @var array
     */
    private $entries;

    /**
     * Constructs a new EmailAddresses element
     * @return null
     */
    public function __construct() {
        $this->entries = array();
    }

    /**
     * Adds an entry to this collection element
     * @param EmailAddressDictionaryEntry $entry
     * @return null
     */
    public function addEntry(EmailAddressDictionaryEntry $entry) {
        $this->entries[] = $entry;
    }

    /**
     * Gets the XML of the EmailAddresses element
     * @param EmailAddressDictionary $dictionary
     * @return string
     */
    public static function toXml(EmailAddressDictionary $dictionary) {
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