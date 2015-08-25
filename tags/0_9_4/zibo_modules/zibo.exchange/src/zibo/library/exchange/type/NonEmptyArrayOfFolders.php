<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \Countable;
use \DOMDocument;

/**
 * The Folders element contains an array of folders that are used in folder operations.
 */
abstract class NonEmptyArrayOfFolders implements Countable {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the Folder elements
     * @var array
     */
    private $folders;

    /**
     * Constructs a new NonEmptyArrayOfFolders element
     * @param string $name Name for the XML element of this type
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->folders = array();
    }

    /**
     * Adds a folder to this array
     * @param BaseFolder $folder
     * @return null
     */
    public function addFolder(BaseFolder $folder) {
        $this->folders[] = $folder;
    }

    /**
     * Implementation of Countable::count()
     * @return int
     */
    public function count() {
        return count($this->folders);
    }

    /**
     * Gets the XML of a TargetFolderId
     * @param NonEmptyArrayOfFolders $folders
     * @return string
     */
    public static function toXml(NonEmptyArrayOfFolders $folders) {
        $dom = $folders->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $folders = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->folders as $folder) {
            $folderDom = $folder->toDom();
            $folderElement = $dom->importNode($folderDom->documentElement, true);
            $folders->appendChild($folderElement);
        }

        $dom->appendChild($folders);

        return $dom;
    }

}