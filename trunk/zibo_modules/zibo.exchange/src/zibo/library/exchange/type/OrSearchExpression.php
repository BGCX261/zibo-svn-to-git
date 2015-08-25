<?php

namespace zibo\library\exchange\type;

/**
 * The Or element represents a search expression that performs a logical OR on the search expression that it contains.
 * Or will return true if any of its children return true. Or must have two or more children.
 */
class OrSearchExpression extends MultipleOperandBooleanExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Or';

    /**
     * Constructs a new Or element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}