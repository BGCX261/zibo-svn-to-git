<?php

namespace zibo\library\exchange\type;

/**
 * The IsEqualTo element represents a search expression that compares a property with either a constant value or another property and evaluates to true if they are equal.
 */
class IsEqualTo extends TwoOperandExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'IsEqualTo';

    /**
     * Constructs a new IsEqualTo element
     * @param PathToUnindexedField $fieldURI
     * @param FieldURIOrConstant $fieldURIOrConstant
     * @return null
     */
    public function __construct(PathToUnindexedField $fieldURI, FieldURIOrConstant $fieldURIOrConstant) {
        parent::__construct(self::NAME, $fieldURI, $fieldURIOrConstant);
    }

}