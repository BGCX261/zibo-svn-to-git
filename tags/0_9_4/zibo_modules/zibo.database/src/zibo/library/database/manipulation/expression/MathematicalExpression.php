<?php

namespace zibo\library\database\manipulation\expression;

/**
 * Mathematical expression.
 */
class MathematicalExpression extends AliasExpression {

    /**
     * Addition operator
     * @var string
     */
    const OPERATOR_ADDITION = '+';

    /**
     * Substraction operator
     * @var string
     */
    const OPERATOR_SUBSTRACTION = '-';

    /**
     * Multiplication operator
     * @var string
     */
    const OPERATOR_MULTIPLICATION = '*';

    /**
     * Division operator
     * @var string
     */
    const OPERATOR_DIVISION = '/';

    /**
     * Modulo operator
     * @var string
     */
    const OPERATOR_MODULO = '%';

    /**
     * Exponentiation operator
     * @var string
     */
    const OPERATOR_EXPONENTIATION = '^';

    /**
     * Charachter to open a nested mathematical expression
     * @var string
     */
    const NESTED_OPEN = '(';

    /**
     * Charachter to close a nested mathematical expression
     * @var string
     */
    const NESTED_CLOSE = ')';

    /**
     * Array with the expression parts
     * @var array
     */
    private $parts = array();

    /**
     * Construct a new mathematical expression
     * @param string $alias
     * @return null
     */
    public function __construct($alias = null) {
        $this->setAlias($alias);
    }

    /**
     * Add a expression to this expression
     * @param Expression $expression
     * @param string $operator mathematical operator
     * @return null
     */
    public function addExpression(Expression $expression, $operator = null) {
        $part = new MathematicalExpressionPart($expression, $operator);
        $this->parts[] = $part;
    }

    /**
     * Get the parts of this expression
     * @return array array with MathematicalExpressionPart objects
     */
    public function getParts() {
        return $this->parts;
    }

}