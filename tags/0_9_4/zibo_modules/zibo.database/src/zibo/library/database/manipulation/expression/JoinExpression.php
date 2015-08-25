<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\condition\Condition;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Table join definition
 */
class JoinExpression extends Expression {

    /**
     * Type for a LEFT JOIN
     * @var string
     */
    const TYPE_LEFT = 'LEFT';

    /**
     * Type for a INNER JOIN
     * @var string
     */
    const TYPE_INNER = 'INNER';

    /**
     * Type for a RIGHT JOIN
     * @var string
     */
    const TYPE_RIGHT = 'RIGHT';

    /**
     * The type of this join
     * @var string
     */
    private $type;

    /**
     * The table to join with
     * @var TableExpression
     */
    private $table;

    /**
     * The condition of this join expression
     * @var zibo\library\database\manipulation\expression\condition\Condition
     */
    private $condition;

    /**
     * Construct a new join expression
     * @param string $type join type (INNER, LEFT or RIGHT)
     * @param TableExpression $table table to join with
     * @param zibo\library\database\manipulation\expression\condition\Condition $condition join condition
     * @return null
     */
    public function __construct($type, TableExpression $table, Condition $condition) {
        $this->setType($type);
        $this->table = $table;
        $this->condition = $condition;
    }

    /**
     * Set the join type
     * @param string $type possible values are INNER, LEFT and RIGHT
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the type is empty or not valid type
     */
    private function setType($type) {
        try {
            if (String::isEmpty($type)) {
                throw new DatabaseException('Provided type is empty');
            }
        } catch (ZiboException $e) {
            throw new DatabaseException('Provided type is not a string');
        }

        if ($type != self::TYPE_LEFT && $type != self::TYPE_INNER && $type != self::TYPE_RIGHT) {
            throw new DatabaseException($type . ' is not a valid type, try ' . self::TYPE_LEFT . ', ' . self::TYPE_INNER . ' or ' . self::TYPE_RIGHT);
        }

        $this->type = $type;
    }

    /**
     * Get the join type
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get the table to join with
     * @return TableExpression
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Get the condition of this join
     * @return zibo\library\database\manipulation\expression\condition\Condition
     */
    public function getCondition() {
        return $this->condition;
    }

}