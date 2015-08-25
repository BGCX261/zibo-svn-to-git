<?php

namespace zibo\library\exchange\type;

/**
 * The IsLessThanOrEqualTo element represents a search expression that compares a property with either a constant value or another property and returns true if the first property is less than or equal to the second.
 */
class IsLessThanOrEqualTo extends TwoOperandExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'IsLessThanOrEqualTo';

    /**
     * Constructs a new IsLessThanOrEqualTo element
     * @param PathToUnindexedField $fieldURI
     * @param FieldURIOrConstant $fieldURIOrConstant
     * @return null
     */
    public function __construct(PathToUnindexedField $fieldURI, FieldURIOrConstant $fieldURIOrConstant) {
        parent::__construct(self::NAME, $fieldURI, $fieldURIOrConstant);
    }

}