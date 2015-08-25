<?php

namespace zibo\library\exchange\type;

/**
 * The And element represents a search expression that allows you to perform a Boolean AND operation between two or more search expressions.
 * The result of the AND operation is true if all the search expressions contained within the And element are true.
 */
class AndSearchExpression extends MultipleOperandBooleanExpression {

    /**
     * Name of this element
     * @var string
     */
    const NAME = 'And';

    /**
     * Constructs a new And element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}