<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The Contact element represents a contact item in the Exchange store.
 */
class Contact extends Item {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Contact';

    /**
     * Name for the FileAs element
     * @var string
     */
    const ELEMENT_FILE_AS = 'FileAs';

    /**
     * Name for the Surname element
     * @var string
     */
    const ELEMENT_SURNAME = 'Surname';

    /**
     * Name for the GivenName element
     * @var string
     */
    const ELEMENT_GIVEN_NAME = 'GivenName';

    /**
     * Name for the CompanyName element
     * @var string
     */
    const ELEMENT_COMPANY_NAME = 'CompanyName';

    /**
     * Name for the JobTitle element
     * @var string
     */
    const ELEMENT_JOB_TITLE = 'JobTitle';

    /**
     * Name for the PhysicalAddresses element
     * @var string
     */
    const ELEMENT_PHYSICAL_ADDRESSES = 'PhysicalAddresses';

    /**
     * Name for the PhoneNumbers element
     * @var string
     */
    const ELEMENT_PHONE_NUMBERS = 'PhoneNumbers';

    /**
     * Name for the EmailAddresses element
     * @var string
     */
    const ELEMENT_EMAIL_ADDRESSES = 'EmailAddresses';

    /**
     * Value for the FileAs element
     * @var string
     */
    public $FileAs;

    /**
     * Value for the SurName element
     * @var string
     */
    public $Surname;

    /**
     * Value for the GivenName element
     * @var string
     */
    public $GivenName;

    /**
     * Value for the CompanyName element
     * @var string
     */
    public $CompanyName;

    /**
     * Value for the PhysicalAddresses element
     * @var PhysicalAddressDictionary
     */
    public $PhysicalAddresses;

    /**
     * Value for the EmailAddresses element
     * @var EmailAddressDictionary
     */
    public $EmailAddresses;

    /**
     * Value for the PhoneNumbers element
     * @var PhoneNumberDictionary
     */
    public $PhoneNumbers;

    /**
     * Value for the JobTitle element
     * @var string
     */
    public $JobTitle;

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $contactItem = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);

        if ($this->FileAs) {
            $contactItem->appendChild($dom->createElement(self::ELEMENT_FILE_AS, htmlspecialchars($this->FileAs)));
        }

        if ($this->GivenName) {
            $contactItem->appendChild($dom->createElement(self::ELEMENT_GIVEN_NAME, htmlspecialchars($this->GivenName)));
        }

        if ($this->CompanyName) {
            $contactItem->appendChild($dom->createElement(self::ELEMENT_COMPANY_NAME, htmlspecialchars($this->CompanyName)));
        }

        if ($this->EmailAddresses) {
            $emailAddressesDom = $this->EmailAddresses->toDom();

            $emailAddressesElement = $dom->importNode($emailAddressesDom->documentElement, true);

            $contactItem->appendChild($emailAddressesElement);
        }

        if ($this->PhysicalAddresses) {
            $physicalAddressesDom = $this->PhysicalAddresses->toDom();

            $physicalAddressesElement = $dom->importNode($physicalAddressesDom->documentElement, true);

            $contactItem->appendChild($physicalAddressesElement);
        }

        if ($this->PhoneNumbers) {
            $phoneNumbersDom = $this->PhoneNumbers->toDom();

            $phoneNumbersElement = $dom->importNode($phoneNumbersDom->documentElement, true);

            $contactItem->appendChild($phoneNumbersElement);
        }

        if ($this->JobTitle) {
            $contactItem->appendChild($dom->createElement(self::ELEMENT_JOB_TITLE, htmlspecialchars($this->JobTitle)));
        }

        if ($this->Surname) {
            $contactItem->appendChild($dom->createElement(self::ELEMENT_SURNAME, htmlspecialchars($this->Surname)));
        }

        $dom->appendChild($contactItem);

        return $dom;
    }

}