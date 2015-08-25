<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The Constant element identifies a constant value in a restriction.
 */
class ConstantValue {

    /**
     * Name of this element
     * @var string
     */
    const NAME = 'Constant';

    /**
     * Name of the value attribute
     * @var string
     */
    const ATTRIBUTE_VALUE = 'Value';

    /**
     * Specifies the value to compare in the restriction.
     * @var string
     */
    public $value;

    /**
     * Constructs a Constant element
     * @param string $value
     * @return null
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $constant = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);
        $constant->setAttribute(self::ATTRIBUTE_VALUE, htmlspecialchars($this->value));

        $dom->appendChild($constant);

        return $dom;
    }

}