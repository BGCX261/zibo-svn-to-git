<?php

namespace zibo\library\database\manipulation\condition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\expression\Expression;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Simple condition in the form of [expression 1] [comparison operator] [expression 2].
 * eg. city LIKE 'A%'
 */
class SimpleCondition extends Condition {

    /**
     * Comparison operator of this condition
     * @var string
     */
    private $operator;

    /**
     * Expression left of the operator
     * @var zibo\library\database\manipulation\expression\Expression
     */
    private $expressionLeft;

    /**
     * Expression right of the operator
     * @var zibo\library\database\manipulation\expression\Expression
     */
    private $expressionRight;

    /**
     * Constructs a new condition
     * @param zibo\library\database\manipulation\expression\Expression $expressionLeft expression left of the comparison operator
     * @param zibo\library\database\manipulation\expression\Expression $expressionRight expression right of the comparison operator
     * @param string $operator comparison operator between the fields (=, <, >, ...) (default =)
     * @return null
     */
    public function __construct(Expression $expressionLeft, Expression $expressionRight, $operator = null) {
        $this->expressionLeft = $expressionLeft;
        $this->expressionRight = $expressionRight;
        $this->setOperator($operator);
    }

    /**
     * Sets the comparison operator of this condition
     * @param string $operator Operator to compare the fields
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the operator is empty or not a valid string
     */
    protected function setOperator($operator = null) {
    	if ($operator === null) {
    		$this->operator = self::OPERATOR_EQUALS;
    		return;
    	}

    	try {
	    	if (!String::isString($operator, String::NOT_EMPTY)) {
	    		throw new DatabaseException('Provided operator is empty');
	    	}
    	} catch (ZiboException $e) {
    		throw new DatabaseException('Provided operator is not a valid string');
    	}

    	$this->operator = strtoupper($operator);
    }

    /**
     * Gets the comparison operator of this condition
     * @return string Operator to compare the expressions
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * Gets expression left of the comparison operator
     * @return zibo\library\database\manipulation\expression\Expression
     */
    public function getLeftExpression() {
        return $this->expressionLeft;
    }

    /**
     * Gets expression right of the comparison operator
     * @return zibo\library\database\manipulation\expression\Expression
     */
    public function getRightExpression() {
        return $this->expressionRight;
    }

}