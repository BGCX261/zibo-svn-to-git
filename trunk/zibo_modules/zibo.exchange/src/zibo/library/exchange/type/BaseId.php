<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;
use zibo\library\String;

use \DOMDocument;

/**
 * The abstract BaseId element contains the unique identifier and change key of an item in the Exchange store.
 */
abstract class BaseId {

    /**
     * Name of the id attribute
     * @var string
     */
    const ATTRIBUTE_ID = 'Id';

    /**
     * Name of the change key attribute
     * @var string
     */
    const ATTRIBUTE_CHANGE_KEY = 'ChangeKey';

    /**
     * Name for the element of this type
     * @var string
     */
    protected $name;

    /**
     * Id of this id
     * @var string
     */
    public $Id;

    /**
     * Change key of this id
     * @var string
     */
    public $ChangeKey;

    /**
     * Constructs a new id
     * @param string $name Name for the element of this type
     * @param string $id Id of this id
     * @param string $changeKey Change key of this id
     * @return null
     */
    public function __construct($name, $id, $changeKey = null) {
        $this->name = $name;

        $this->setId($id);
        $this->setChangeKey($changeKey);
    }

    /**
     * Sets the id
     * @param string $id
     * @return null
     * @throws InvalidArgumentException when the id is empty
     */
    public function setId($id) {
        if (String::isEmpty($id)) {
            throw new InvalidArgumentException('Provided id is empty');
        }

        $this->Id = $id;
    }

    /**
     * Sets the change key
     * @param string $changeKey
     * @return null
     * @throws InvalidArgumentException when the changeKey is empty
     */
    public function setChangeKey($changeKey) {
        if ($changeKey !== null && String::isEmpty($changeKey)) {
            throw new InvalidArgumentException('Provided change key is empty');
        }

        $this->ChangeKey = $changeKey;
    }

    /**
     * Gets the XML of the provided base id
     * @param BaseId $baseId
     * @return string
     */
    public static function toXml(BaseId $baseId) {
        $dom = $baseId->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this base id
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $id = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);
        $id->setAttribute(self::ATTRIBUTE_ID, $this->Id);

        if ($this->ChangeKey) {
            $id->setAttribute(self::ATTRIBUTE_CHANGE_KEY, $this->ChangeKey);
        }

        $dom->appendChild($id);

        return $dom;
    }

}