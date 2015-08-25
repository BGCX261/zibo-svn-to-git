<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * Abstract two operand search expression
 */
abstract class TwoOperandExpression extends SearchExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * First operand of the expression
     * @var PathToUnindexedField
     */
    protected $fieldURI;

    /**
     * Second operand of the expression
     * @var FieldURIOrConstant
     */
    protected $fieldURIOrConstant;

    /**
     * Constructs a new two operand search expression
     * @param string $name Name for the XML element
     * @param PathToUnindexedField $fieldURI First operand of the expression
     * @param FieldURIOrConstant $fieldURIOrConstant Second operand of the expression
     * @return null
     */
    public function __construct($name, PathToUnindexedField $fieldURI, FieldURIOrConstant $fieldURIOrConstant) {
        $this->name = $name;
        $this->fieldURI = $fieldURI;
        $this->fieldURIOrConstant = $fieldURIOrConstant;
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $expression = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        $fieldURIDom = $this->fieldURI->toDom();
        $fieldURIElement = $dom->importNode($fieldURIDom->documentElement, true);
        $expression->appendChild($fieldURIElement);

        $fieldURIOrConstantDom = $this->fieldURIOrConstant->toDom();
        $fieldURIOrConstantElement = $dom->importNode($fieldURIOrConstantDom->documentElement, true);
        $expression->appendChild($fieldURIOrConstantElement);

        $dom->appendChild($expression);

        return $dom;
    }

}