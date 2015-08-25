<?php

namespace zibo\library\database\manipulation\condition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Part of a nested condition
 */
class NestedConditionPart {

    /**
     * Logical operator for the condition
     * @var string
     */
    private $operator;

    /**
     * Condition of this part
     * @var Condition
     */
    private $condition;

    /**
     * Constructs a new part of a nested condition
     * @param Condition $condition Condition of this part
     * @param string $operator Logical operator used before this part
     * @return null
     */
    public function __construct(Condition $condition, $operator = null) {
        $this->condition = $condition;
        $this->setOperator($operator);
    }

    /**
     * Gets the condition of this part
     * @return Condition
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * Gets the logical operator of this condition
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * Sets the logical operator of this condition
     * @param string $operator Logical operator used before this part
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the operator is not AND or OR
     */
    private function setOperator($operator = null) {
        if ($operator === null) {
            $operator = Condition::OPERATOR_AND;
        } else {
            if (!String::isString($operator, String::NOT_EMPTY)) {
                throw new DatabaseException('Provided logical operator is empty. Try AND or OR');
            }

            $operator = strtoupper($operator);
            if ($operator != Condition::OPERATOR_AND && $operator != Condition::OPERATOR_OR) {
                throw new DatabaseException('Provided logical operator is invalid. Try AND or OR.');
            }
        }

        $this->operator = $operator;
    }

}