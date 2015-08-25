<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \Countable;
use \DOMDocument;

/**
 * The FolderIds element contains an array of folder identifiers that are used to identify folders to copy, move, get, delete, or monitor for event notifications.
 */
abstract class NonEmptyArrayOfBaseFolderIds implements Countable {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the folder ids of this element
     * @var array
     */
    protected $folderIds;

    /**
     * Constructs a new array of folder ids
     * @param string $name Name for the XML element
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->folderIds = array();
    }

    /**
     * Adds a folder if to this array
     * @param BaseFolderId $folderId
     */
    public function addFolderId(BaseFolderId $folderId) {
        $this->folderIds[] = $folderId;
    }

    /**
     * Implementation of Countable::count()
     * @return int
     */
    public function count() {
        return count($this->array);
    }

    /**
     * Gets the XML of a TargetFolderId
     * @param TargetFolderId $folderId
     * @return string
     */
    public static function toXml(NonEmptyArrayOfBaseFolderIds $folderIds) {
        $dom = $folderIds->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $folderIds = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->folderIds as $folderId) {
            $folderIdDom = $folderId->toDom();
            $folderIdElement = $dom->importNode($folderIdDom->documentElement, true);
            $folderIds->appendChild($folderIdElement);
        }

        $dom->appendChild($folderIds);

        return $dom;
    }

}