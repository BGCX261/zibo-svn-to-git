<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\condition\Condition;

/**
 * Base class for a conditional statement
 */
abstract class ConditionalStatement extends TableStatement {

    /**
     * Conditions of this statement
     * @var array
     */
    protected $conditions = array();

    /**
     * Logical operator for the conditions
     * @var string
     */
    protected $operator = Condition::OPERATOR_AND;

    /**
     * Adds a condition to this statement
     * @param zibo\library\database\manipulation\expression\condition\Condition $condition
     * @return null
     */
    public function addCondition(Condition $condition) {
        $this->conditions[] = $condition;
    }

    /**
     * Gets the conditions of this statement
     * @return array Array with Condition objects
     */
    public function getConditions() {
        return $this->conditions;
    }

    /**
     * Sets the logical operator for the conditions of this statement
     * @param string $operator Logical operator
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the operator is not AND or OR
     */
    public function setOperator($operator = null) {
        if ($operator === null) {
            $operator = Condition::OPERATOR_AND;
        } elseif ($operator != Condition::OPERATOR_AND && $operator != Condition::OPERATOR_OR) {
            throw new DatabaseException('Provided logical operator is invalid. Try AND or OR.');
        }

        $this->operator = $operator;
    }

    /**
     * Gets the logical operator for the conditions of this statement
     * @return string Logical operator
     */
    public function getOperator() {
        return $this->operator;
    }

}