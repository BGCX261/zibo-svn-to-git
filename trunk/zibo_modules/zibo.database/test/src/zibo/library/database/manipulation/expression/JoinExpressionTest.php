<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\manipulation\condition\Condition;
use zibo\library\database\manipulation\condition\SimpleCondition;

use zibo\test\BaseTestCase;

class JoinExpressionTest extends BaseTestCase {

    private $condition;
    private $table;
    private $type;

    public function setUp() {
        $this->condition = new SimpleCondition(new SqlExpression('field1'), new SqlExpression('field2'), Condition::OPERATOR_EQUALS);
        $this->table = new TableExpression('table');
        $this->type = JoinExpression::TYPE_LEFT;
    }

    public function testConstruct() {
        $join = new JoinExpression($this->type, $this->table, $this->condition);

        $this->assertEquals($this->type, $join->getType());
        $this->assertEquals($this->table, $join->getTable());
        $this->assertEquals($this->condition, $join->getCondition());
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyTypePassed() {
        new JoinExpression('', $this->table, $this->condition);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenInvalidTypePassed() {
        new JoinExpression('test', $this->table, $this->condition);
    }

}