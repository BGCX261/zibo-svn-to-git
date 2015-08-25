<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Order clause of a statement
 */
class OrderExpression extends Expression {

    /**
     * Order direction ascending
     * @var string
     */
    const DIRECTION_ASC = 'ASC';

    /**
     * Order direction descending
     * @var string
     */
    const DIRECTION_DESC = 'DESC';

    /**
     * Expression of this order clause
     * @var Expression
     */
    private $expression;

    /**
     * Direction of this order clause
     * @var string
     */
    private $direction;

    /**
     * Construct the order clause
     * @param Expression $expression of this order clause
     * @param string $direction order direction (ASC or DESC) (default: ASC)
     * @return null
     */
    public function __construct(Expression $expression, $direction = null) {
        $this->setExpression($expression);
        $this->setDirection($direction);
    }

    /**
     * Set the expression of this order clause
     * @param Expression expression
     * @return null
     */
    private function setExpression(Expression $expression) {
        $this->expression = $expression;
    }

    /**
     * Get the expression of this order clause
     * @return Expression expression
     */
    public function getExpression() {
        return $this->expression;
    }

    /**
     * Set the direction of this order clause
     * @param string direction
     * @return null
     * @throws zibo\ZiboException when $direction is not a valid string
     * @throws zibo\library\database\exception\DatabaseException when the direction is empty or invalid
     */
    private function setDirection($direction = null) {
        if ($direction === null) {
            $direction = self::DIRECTION_ASC;
        }

        if (String::isEmpty($direction)) {
            throw new DatabaseException('Provided direction is empty');
        }

        if ($direction != self::DIRECTION_ASC && $direction != self::DIRECTION_DESC) {
            throw new DatabaseException('Provided direction is invalid, try ' . self::DIRECTION_ASC . ' or ' . self::DIRECTION_DESC);
        }

        $this->direction = $direction;
    }

    /**
     * Get the order direction
     * @return string order direction
     */
    public function getDirection() {
        return $this->direction;
    }

}