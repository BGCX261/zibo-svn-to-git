<?php

namespace zibo\library\exchange\type;

/**
 * The IsNotEqualTo element represents a search expression that compares a property with either a constant value or another property and returns true if the values are not the same.
 */
class IsNotEqualTo extends TwoOperandExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'IsNotEqualTo';

    /**
     * Constructs a new IsNotEqualTo element
     * @param PathToUnindexedField $fieldURI
     * @param FieldURIOrConstant $fieldURIOrConstant
     * @return null
     */
    public function __construct(PathToUnindexedField $fieldURI, FieldURIOrConstant $fieldURIOrConstant) {
        parent::__construct(self::NAME, $fieldURI, $fieldURIOrConstant);
    }

}