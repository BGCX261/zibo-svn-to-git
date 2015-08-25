<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\manipulation\condition\Condition;

/**
 * Expression for a CASE in a query
 */
class CaseExpression extends AliasExpression {

    /**
     * Array with WhenExpression objects
     * @var array
     */
    private $when = array();

    /**
     * Expression to return when no conditions are met
     * @var Expression
     */
    private $defaultExpression = null;

    /**
     * Set the expression to return when no conditions are met
     * @param Expression $expression
     * @return null
     */
    public function setDefaultExpression(Expression $expression) {
        $this->defaultExpression = $expression;
    }

    /**
     * Get the expression to return when no conditions are met
     * @return Expression
     */
    public function getDefaultExpression() {
        return $this->defaultExpression;
    }

    /**
     * Add a WHEN part to this expression
     * @param WhenExpression $whenExpression
     */
    public function addWhen(Condition $condition, Expression $expression) {
        $whenExpression = new WhenExpression($condition, $expression);
        $this->when[] = $whenExpression;
    }

    /**
     * Get all the WHEN parts of this expression
     * @return array Array with WhenExpression objects
     */
    public function getWhen() {
        return $this->when;
    }

}