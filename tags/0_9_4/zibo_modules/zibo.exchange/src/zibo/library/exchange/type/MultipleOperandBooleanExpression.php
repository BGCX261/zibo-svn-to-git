<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * Abstract implementation of a search expression which contains multiple search expressions
 */
abstract class MultipleOperandBooleanExpression extends SearchExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    protected $name;

    /**
     * Array with the search expressions of this expression
     * @var array
     */
    protected $searchExpressions;

    /**
     * Constructs a new search expression element
     * @param string $name Name for the XML element of this type
     * @return null
     */
    public function __construct($name) {
        $this->name = $name;
        $this->searchExpressions = array();
    }

    /**
     * Adds a search expression to this expression
     * @param SearchExpression $searchExpression
     * @return null
     */
    public function addSearchExpression(SearchExpression $searchExpression) {
        $this->searchExpressions[] = $searchExpression;
    }

    /**
     * Gets the DOM document of this element
     * @return DOMDocument
     */
    public function toDom() {
        $dom = new DOMDocument('1.0', 'utf-8');

        $multipleOperandExpression = $dom->createElementNS(Client::SCHEMA_TYPES, $this->name);

        foreach ($this->searchExpressions as $searchExpression) {
            $searchExpressionDom = $searchExpression->toDom();
            $searchExpressionElement = $dom->importNode($searchExpressionDom->documentElement, true);
            $multipleOperandExpression->appendChild($searchExpressionElement);
        }

        $dom->appendChild($multipleOperandExpression);

        return $dom;
    }

}