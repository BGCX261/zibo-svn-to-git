<?php

namespace zibo\library\database\manipulation\condition;

/**
 * Base class for a condition
 */
abstract class Condition {

    /**
     * Logical operator AND
     * @var string
     */
    const OPERATOR_AND = 'AND';

    /**
     * Logical operator OR
     * @var string
     */
    const OPERATOR_OR = 'OR';

    /**
     * Comparison operator equals
     * @var string
     */
    const OPERATOR_EQUALS = '=';

    /**
     * Comparison operator less
     * @var string
     */
    const OPERATOR_LESS = '<';

    /**
     * Comparison operator greater
     * @var string
     */
    const OPERATOR_GREATER = '>';

    /**
     * Comparison operator less or equals
     * @var string
     */
    const OPERATOR_LESS_OR_EQUALS = '<=';

    /**
     * Comparison operator greater or equals
     * @var string
     */
    const OPERATOR_GREATER_OR_EQUALS = '>=';

    /**
     * Comparison operator not equals
     * @var string
     */
    const OPERATOR_NOT_EQUALS = '<>';

    /**
     * Comparison operator LIKE
     * @var string
     */
    const OPERATOR_LIKE = 'LIKE';

    /**
     * Comparison operator IS
     * @var string
     */
    const OPERATOR_IS = 'IS';

    /**
     * Comparison operator IN
     * @var string
     */
    const OPERATOR_IN = 'IN';

    /**
     * Comparison operator BETWEEN
     * @var string
     */
    const OPERATOR_BETWEEN = 'BETWEEN';

    /**
     * Comparison operator NOT
     * @var string
     */
    const OPERATOR_NOT = 'NOT';

}