<?php

namespace zibo\library\exchange\type;

/**
 * The IsGreaterThan element represents a search expression that compares a property with either a constant value or another property and returns true if the first property is greater.
 */
class IsGreaterThan extends TwoOperandExpression {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'IsGreaterThan';

    /**
     * Constructs a new IsGreaterThan element
     * @param PathToUnindexedField $fieldURI
     * @param FieldURIOrConstant $fieldURIOrConstant
     * @return null
     */
    public function __construct(PathToUnindexedField $fieldURI, FieldURIOrConstant $fieldURIOrConstant) {
        parent::__construct(self::NAME, $fieldURI, $fieldURIOrConstant);
    }

}