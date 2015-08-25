<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\manipulation\condition\Condition;

/**
 * Expression for the WHEN part of a CASE expession
 */
class WhenExpression extends Expression {

    /**
     * Condition for the when
     * @var zibo\library\database\manipulation\expression\condition\Condition
     */
    private $condition;

    /**
     * Expression when the condition is true
     * @var Expression
     */
    private $expression;

    /**
     * Construct a new WHEN part
     * @param Condition $condition Condition for this part
     * @param Expression $expression Expression when the condition is true
     * @return null
     */
    public function __construct(Condition $condition, Expression $expression) {
        $this->condition = $condition;
        $this->expression = $expression;
    }

    /**
     * Get the condition of this WHEN part
     * @return Condition
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * Get the expression for when the condition is true
     * @return Expression expression to return if the condition is true
     */
    public function getExpression() {
        return $this->expression;
    }

}