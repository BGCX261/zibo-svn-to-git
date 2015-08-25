<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The Restriction element represents the restriction or query that is used to filter items or folders in FindItem/FindFolder and search folder operations.
 */
class Restriction {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Restriction';

    /**
     * Search expression for this restriction
     * @var array
     */
    protected $searchExpressions;

    /**
     * Constructs a new Restriction element
     * @return null
     */
    public function __construct() {
        $this->searchExpressions = array();
    }

    /**
     * Adds a search expression to this restriction
     * @param SearchExpression $searchExpression
     * @return null
     */
    public function addSearchExpression(SearchExpression $searchExpression) {
        $this->searchExpressions[] = $searchExpression;
    }

    /**
     * Gets the XML of a Restriction
     * @param Restriction $restriction
     * @return string
     */
    public static function toXml(Restriction $restriction) {
        $dom = $restriction->toDom();
        return $dom->saveXML();
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $multipleOperandExpression = $dom->createElementNS(Client::SCHEMA_TYPES, self::NAME);

        foreach ($this->searchExpressions as $searchExpression) {
            $searchExpressionDom = $searchExpression->toDom();
            $searchExpressionElement = $dom->importNode($searchExpressionDom->documentElement, true);
            $multipleOperandExpression->appendChild($searchExpressionElement);
        }

        $dom->appendChild($multipleOperandExpression);

        return $dom;
    }

}