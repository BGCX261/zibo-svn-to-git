<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;
use \InvalidArgumentException;

/**
 * The Entry element describes a single physical address for a contact item.
 */
class PhysicalAddressDictionaryEntry extends Entry {

    /**
     * Identifier for business address
     * @var string
     */
    const KEY_BUSINESS = 'Business';

    /**
     * Identifier for home address
     * @var string
     */
    const KEY_HOME = 'Home';

    /**
     * Identifier for other address
     * @var string
     */
    const KEY_OTHER = 'Other';

    /**
     * Name of the street element
     * @var string
     */
    const ELEMENT_STREET = 'Street';

    /**
     * Name of the city element
     * @var string
     */
    const ELEMENT_CITY = 'City';

    /**
     * Name of the state element
     * @var string
     */
    const ELEMENT_STATE = 'State';

    /**
     * Name of the country or region element
     * @var string
     */
    const ELEMENT_COUNTRY_OR_REGION = 'CountryOrRegion';

    /**
     * Name of the postal code element
     * @var string
     */
    const ELEMENT_POSTAL_CODE = 'PostalCode';

    /**
     * Represents a street address for a contact item.
     * @var string
     */
    public $Street;

    /**
     * Represents the city name that is associated with a contact.
     * @var string
     */
    public $City;

    /**
     * Represents the state of residence for a contact item.
     * @var string
     */
    public $State;

    /**
     * Represents the country or region for a given physical address.
     * @var string
     */
    public $CountryOrRegion;

    /**
     * Represents the postal code for a contact item.
     * @var string
     */
    public $PostalCode;

    /**
     * Sets the key of this entry
     * @param string $key
     * @return null
     * @throws InvalidArgumentException when the key is not Business, Home or Other
     */
    public function setKey($key) {
        $keys = array(
            self::KEY_BUSINESS,
            self::KEY_HOME,
            self::KEY_OTHER,
        );

        if (!in_array($key, $keys)) {
            throw new InvalidArgumentException('Provided key is invalid, try one of the constants');
        }

        $this->Key = $key;
    }

    /**
     * Gets a DOM element of this folder
     * @return \DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $entry = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);
        $entry->setAttribute(self::ATTRIBUTE_KEY, $this->Key);

        if ($this->Street) {
            $street = $dom->createElement(self::ELEMENT_STREET, htmlspecialchars($this->Street));
            $entry->appendChild($street);
        }

        if ($this->City) {
            $city = $dom->createElement(self::ELEMENT_CITY, htmlspecialchars($this->City));
            $entry->appendChild($city);
        }

        if ($this->State) {
            $state = $dom->createElement(self::ELEMENT_STATE, htmlspecialchars($this->State));
            $entry->appendChild($state);
        }

        if ($this->CountryOrRegion) {
            $country = $dom->createElement(self::ELEMENT_COUNTRY_OR_REGION, htmlspecialchars($this->CountryOrRegion));
            $entry->appendChild($country);
        }

        if ($this->PostalCode) {
            $postalCode = $dom->createElement(self::ELEMENT_POSTAL_CODE, htmlspecialchars($this->PostalCode));
            $entry->appendChild($postalCode);
        }

        $dom->appendChild($entry);

        return $dom;
    }

}