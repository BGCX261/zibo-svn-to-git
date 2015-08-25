<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * Abstract entry for a dictionary
 */
class Entry {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Entry';

    /**
     * Name for the Key attribute
     * @var string
     */
    const ATTRIBUTE_KEY = 'Key';

    /**
     * The key of this entry
     * @var string
     */
    public $Key;

    /**
     * The value of the entry
     * @var string
     */
    public $value;

    /**
     * Constructs a new Entry element
     * @param string $key Key for the entry value
     * @param stirng $value Value for the entry
     * @return null
     */
    public function __construct($key, $value = null) {
        $this->setKey($key);

        $this->value = $value;
    }

    /**
     * Sets the key of this entry
     * @param string $key
     * @return null
     */
    public function setKey($key) {
        $this->Key = $key;
    }

    /**
     * Gets a DOM element of this folder
     * @return \DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $entry = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME, htmlspecialchars($this->value));
        $entry->setAttribute(self::ATTRIBUTE_KEY, $this->Key);

        $dom->appendChild($entry);

        return $dom;
    }

}