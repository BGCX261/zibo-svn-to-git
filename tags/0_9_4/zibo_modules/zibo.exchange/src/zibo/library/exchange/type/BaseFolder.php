<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * Abstract implementation of a Folder element
 */
abstract class BaseFolder {

    /**
     * Name of the DisplayName element
     * @var string
     */
    const ELEMENT_DISPLAY_NAME = 'DisplayName';

    /**
     * Name for the XML element for this type
     * @var string
     */
    protected $name;

    /**
     * The display name of the folder
     * @var string
     */
    public $DisplayName;

    /**
     * Constructs a new Folder element
     * @param string $name Name for the XML element of this type
     * @param string $displayName The display name of the folder
     * @return null
     */
    public function __construct($name, $displayName) {
        $this->name = $name;
        $this->DisplayName = $displayName;
    }

    /**
     * Gets a DOM element of this folder
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $folder = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        if ($this->DisplayName) {
            $displayName = $dom->createElement(self::ELEMENT_DISPLAY_NAME, htmlspecialchars($this->DisplayName));

            $folder->appendChild($displayName);
        }

        $dom->appendChild($folder);

        return $dom;
    }

}