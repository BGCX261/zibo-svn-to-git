<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The TargetFolderId element identifies the folder in which a action is performed.
 */
abstract class TargetFolderId {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Contains the required identifier and the optional change key of a folder
     * @var FolderId
     */
    public $FolderId;

    /**
     * Constructs a new TargetFolderId element
     * @param string $name Name for the XML element of this type
     * @param BaseFolderId $folderId Identifier and the optional change key of a folder
     * @return null
     */
    public function __construct($name, BaseFolderId $folderId) {
        $this->name = $name;
        $this->FolderId = $folderId;
    }

    /**
     * Gets the XML of a TargetFolderId
     * @param TargetFolderId $folderId
     * @return string
     */
    public static function toXml(TargetFolderId $folderId) {
        $dom = $folderId->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $parentFolderId = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        $folderIdDom = $this->FolderId->toDom();
        $folderIdElement = $dom->importNode($folderIdDom->documentElement, true);
        $parentFolderId->appendChild($folderIdElement);

        $dom->appendChild($parentFolderId);

        return $dom;
    }

}
