<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Part of a mathematical expression
 */
class MathematicalExpressionPart {

    /**
     * Mathematical operator for the expression
     * @var string
     */
    private $operator;

    /**
     * Expression of this part
     * @var Expression
     */
    private $expression;

    /**
     * Construct a new part of a mathematical expression
     * @param Expression $expression
     * @param string $operator mathematical operator before the expression
     * @return null
     */
    public function __construct(Expression $expression, $operator = null) {
        $this->expression = $expression;
        $this->setOperator($operator);
    }

    /**
     * Get the expression of this part
     * @return Expression
     */
    public function getExpression() {
        return $this->expression;
    }

    /**
     * Get the mathematical operator for the expression
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * Set the mathematical operator of the expression
     * @param string $operator
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the operator is empty or invalid
     */
    private function setOperator($operator = null) {
        if ($operator === null) {
            $this->operator = MathematicalExpression::OPERATOR_ADDITION;
            return;
        }

        try {
            if (String::isEmpty($operator)) {
                throw new DatabaseException('Provided operator is empty');
            }
        } catch (ZiboException $e) {
            throw new DatabaseException('Provided operator is not a valid string');
        }

        $this->operator = $operator;
    }

}