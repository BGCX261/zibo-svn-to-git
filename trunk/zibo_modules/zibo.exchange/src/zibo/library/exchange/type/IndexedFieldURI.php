<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use zibo\ZiboException;

use \DOMDocument;

/**
 * The IndexedFieldURI element identifies individual members of a dictionary.
 */
class IndexedFieldURI extends PathToUnindexedField {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'IndexedFieldURI';

    /**
     * Name of the field index attribute
     * @var string
     */
    const ATTRIBUTE_FIELD_INDEX = 'FieldIndex';

    /**
     * Represents the message header of an item.
     * @var string
     */
    const ITEM_INTERNET_MESSAGE_HEADER = 'item:InternetMessageHeader';

    /**
     * Represents the instant message address of a contact.
     * @var string
     */
    const CONTACT_IM_ADDRESS = 'contacts:ImAddress';

    /**
     * Represents the street address of a contact.
     * @var street
     */
    const CONTACT_PHYSICAL_ADDRESS_STREET = 'contacts:PhysicalAddress:Street';

    /**
     * Represents the city of a contact.
     * @var string
     */
    const CONTACT_PHYSICAL_ADDRESS_CITY = 'contacts:PhysicalAddress:City';

    /**
     * Represents the state of a contact.
     * @var string
     */
    const CONTACT_PHYSICAL_ADDRESS_STATE = 'contacts:PhysicalAddress:State';

    /**
     * Represents the country of a contact.
     * @var string
     */
    const CONTACT_PHYSICAL_ADDRESS_COUNTRY = 'contacts:PhysicalAddress:CountryOrRegion';

    /**
     * Represents the postal code of a contact.
     * @var string
     */
    const CONTACT_PHYSICAL_ADDRESS_POSTAL_CODE = 'contacts:PhysicalAddress:PostalCode';

    /**
     * Represents the phone number of a contact.
     * @var string
     */
    const CONTACT_PHONE_NUMBER = 'contacts:PhoneNumber';

    /**
     * Represents the e-mail address of a contact.
     * @var string
     */
    const CONTACT_EMAIL_ADDRESS = 'contacts:EmailAddress';

    /**
     * Identifies the member of the dictionary to return. This attribute is required.
     * @var string
     */
    public $FieldIndex;

    /**
     * Constructs a new FieldURI element
     * @param string $fieldURI Identifies the dictionary that contains the member to return. This attribute is required.
     * @param string $fieldIndex Identifies the member of the dictionary to return. This attribute is required.
     * @return null
     */
    public function __construct($fieldURI, $fieldIndex) {
        parent::__construct(self::NAME, $fieldURI);

        $this->FieldIndex = $fieldIndex;
    }

    /**
     * Sets the field URI
     * @param string $fieldURI
     * @return null
     */
    public function setFieldURI($fieldURI) {
        $fieldURIs = array(
            self::ITEM_INTERNET_MESSAGE_HEADER,
            self::CONTACT_IM_ADDRESS,
            self::CONTACT_PHYSICAL_ADDRESS_STREET,
            self::CONTACT_PHYSICAL_ADDRESS_CITY,
            self::CONTACT_PHYSICAL_ADDRESS_STATE,
            self::CONTACT_PHYSICAL_ADDRESS_COUNTRY,
            self::CONTACT_PHYSICAL_ADDRESS_POSTAL_CODE,
            self::CONTACT_PHYSICAL_ADDRESS_POSTAL_CODE,
            self::CONTACT_PHONE_NUMBER,
            self::CONTACT_EMAIL_ADDRESS,
        );

        if (!in_array($fieldURI, $fieldURIs)) {
            throw new ZiboException('Provided field URI is not valid');
        }

        $this->FieldURI = $fieldURI;
    }

    /**
     * Gets the DOM document of this type
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $path = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);
        $path->setAttribute(self::ATTRIBUTE_FIELD_URI, $this->FieldURI);
        $path->setAttribute(self::ATTRIBUTE_FIELD_INDEX, $this->FieldIndex);

        $dom->appendChild($path);

        return $dom;
    }

}