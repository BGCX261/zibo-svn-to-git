<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

use \InvalidArgumentException;

/**
 * The FieldURIOrConstant element represents either a property or a constant value to be used when comparing with another property.
 */
class FieldURIOrConstant {

    /**
     * Name for the XML element of this type
     */
    const NAME = 'FieldURIOrConstant';

    /**
     * The FieldURI or the ConstantValue
     * @var FieldURI|ConstantValue
     */
    private $value;

    /**
     * Construct a new FieldURIOrConstant element
     * @param FieldURI|ConstantValue $value The FieldURI of the ConstantValue
     * @return null
     * @throws InvalidArgumentException
     */
    public function __construct($value) {
        if (!($value instanceof PathToUnindexedField || $value instanceof ConstantValue)) {
            throw new InvalidArgumentException('Provided value is not a FieldURI or a ConstantValue');
        }

        $this->value = $value;
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $fieldURIOrConstant = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);

        $valueDom = $this->value->toDom();
        $valueElement = $dom->importNode($valueDom->documentElement, true);
        $fieldURIOrConstant->appendChild($valueElement);

        $dom->appendChild($fieldURIOrConstant);

        return $dom;
    }

}