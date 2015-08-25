<?php

namespace zibo\library\exchange\type;

use \InvalidArgumentException;

/**
 * The FieldURI element identifies frequently referenced properties by URI.
 */
class FieldURI extends PathToUnindexedField {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'FieldURI';

    /**
     * Identifies the AssistantName property.
     * @var string
     */
    const CONTACT_ASSISTANT_NAME = 'contacts:AssistantName';

    /**
     * Identifies the Birthday property.
     * @var string
     */
    const CONTACT_BIRTHDAY = 'contacts:Birthday';

    /**
     * Identifies the BusinessHomePage property.
     * @var string
     */
    const CONTACT_BUSINESS_HOME_PAGE = 'contacts:BusinessHomePage';

    /**
     * Identifies the Children property.
     * @var string
     */
    const CONTACT_CHILDREN = 'contacts:Children';

    /**
     * Identifies the Companies property.
     * @var string
     */
    const CONTACT_COMPANIES = 'contacts:Companies';

    /**
     * Identifies the CompanyName property.
     * @var string
     */
    const CONTACT_COMPANY_NAME = 'contacts:CompanyName';

    /**
     * Identifies the CompleteName property.
     * @var string
     */
    const CONTACT_COMPLETE_NAME = 'contacts:CompleteName';

    /**
     * Identifies the ContactSource property.
     * @var string
     */
    const CONTACT_CONTACT_SOURCE = 'contacts:ContactSource';

    /**
     * Identifies the Culture property.
     * @var string
     */
    const CONTACT_CULTURE = 'contacts:Culture';

    /**
     * Identifies the Department property.
     * @var string
     */
    const CONTACT_DEPARTMENT = 'contacts:Department';

    /**
     * Identifies the DisplayName property.
     * @var string
     */
    const CONTACT_DISPLAY_NAME = 'contacts:DisplayName';

    /**
     * Identifies the EmailAddresses property.
     * @var unknown_type
     */
    const CONTACT_EMAIL_ADDRESSES = 'contacts:EmailAddresses';

    /**
     * Identifies the FileAs property.
     * @var string
     */
    const CONTACT_FILE_AS = 'contacts:FileAs';

    /**
     * Identifies the FileAsMapping property.
     * @var string
     */
    const CONTACT_FILE_AS_MAPPING = 'contacts:FileAsMapping';

    /**
     * Identifies the Generation property.
     * @var string
     */
    const CONTACT_GENERATION = 'contacts:Generation';

    /**
     * Identifies the GivenName property.
     * @var string
     */
    const CONTACT_GIVEN_NAME = 'contacts:GivenName';

    /**
     * Identifies the ImAddresses property.
     * @var string
     */
    const CONTACT_IM_ADDRESSES = 'contacts:ImAddresses';

    /**
     * Identifies the Initials property.
     * @var string
     */
    const CONTACT_INITIALS = 'contacts:Initials';

    /**
     * Identifies the JobTitle property.
     * @var string
     */
    const CONTACT_JOB_TITLE = 'contacts:JobTitle';

    /**
     * Identifies the Manager property.
     * @var string
     */
    const CONTACT_MANAGER = 'contacts:Manager';

    /**
     * Identifies the MiddleName property.
     * @var string
     */
    const CONTACT_MIDDLE_NAME = 'contacts:MiddleName';

    /**
     * Identifies the Mileage property.
     * @var string
     */
    const CONTACT_MILEAGE = 'contacts:Mileage';

    /**
     * Identifies the Nickname property.
     * @var string
     */
    const CONTACT_NICKNAME = 'contacts:Nickname';

    /**
     * Identifies the OfficeLocation property.
     * @var string
     */
    const CONTACT_OFFICE_LOCATION = 'contacts:OfficeLocation';

    /**
     * Identifies the PhoneNumbers property.
     * @var string
     */
    const CONTACT_PHONE_NUMBERS = 'contacts:PhoneNumbers';

    /**
     * Identifies the PhysicalAddresses property.
     * @var string
     */
    const CONTACT_PHYSICAL_ADDRESSES = 'contacts:PhysicalAddresses';

    /**
     * Identifies the PostalAddressIndex property.
     * @var string
     */
    const CONTACT_POSTAL_ADDRESS_INDEX = 'contacts:PostalAddressIndex';

    /**
     * Identifies the Profession property.
     * @var string
     */
    const CONTACT_PROFESSION = 'contacts:Profession';

    /**
     * Identifies the SpouseName property.
     * @var string
     */
    const CONTACT_SPOUSE_NAME = 'contacts:SpouseName';

    /**
     * Identifies the Surname property.
     * @var string
     */
    const CONTACT_SURNAME = 'contacts:Surname';

    /**
     * Identifies the WeddingAnniversary property.
     * @var string
     */
    const CONTACT_WEDDING_ANNIVERSARY = 'contacts:WeddingAnniversary';

    /**
     * Constructs a new FieldURI element
     * @param string $fieldURI
     * @return null
     */
    public function __construct($fieldURI) {
        parent::__construct(self::NAME, $fieldURI);
    }

    /**
     * Sets the field URI
     * @param string $fieldURI
     * @return null
     * @throws InvalidArgumentException when the provided FieldURI is invalid
     */
    public function setFieldURI($fieldURI) {
        $fieldURIs = array(
            self::CONTACT_ASSISTANT_NAME,
            self::CONTACT_BIRTHDAY,
            self::CONTACT_BUSINESS_HOME_PAGE,
            self::CONTACT_CHILDREN,
            self::CONTACT_COMPANIES,
            self::CONTACT_COMPANY_NAME,
            self::CONTACT_COMPLETE_NAME,
            self::CONTACT_CONTACT_SOURCE,
            self::CONTACT_CULTURE,
            self::CONTACT_DEPARTMENT,
            self::CONTACT_DISPLAY_NAME,
            self::CONTACT_EMAIL_ADDRESSES,
            self::CONTACT_FILE_AS,
            self::CONTACT_FILE_AS_MAPPING,
            self::CONTACT_GENERATION,
            self::CONTACT_GIVEN_NAME,
            self::CONTACT_IM_ADDRESSES,
            self::CONTACT_INITIALS,
            self::CONTACT_JOB_TITLE,
            self::CONTACT_MANAGER,
            self::CONTACT_MIDDLE_NAME,
            self::CONTACT_MILEAGE,
            self::CONTACT_NICKNAME,
            self::CONTACT_OFFICE_LOCATION,
            self::CONTACT_PHONE_NUMBERS,
            self::CONTACT_PHYSICAL_ADDRESSES,
            self::CONTACT_POSTAL_ADDRESS_INDEX,
            self::CONTACT_PROFESSION,
            self::CONTACT_SPOUSE_NAME,
            self::CONTACT_SURNAME,
            self::CONTACT_WEDDING_ANNIVERSARY,
        );

        if (!in_array($fieldURI, $fieldURIs)) {
            throw new InvalidArgumentException('Provided field URI is not valid');
        }

        $this->FieldURI = $fieldURI;
    }

}