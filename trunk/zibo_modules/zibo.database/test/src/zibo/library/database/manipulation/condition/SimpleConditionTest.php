<?php

namespace zibo\library\database\manipulation\condition;

use zibo\library\database\manipulation\expression\SqlExpression;

use zibo\test\BaseTestCase;

class SimpleConditionTest extends BaseTestCase {

    private $expression;

    public function setUp() {
        $this->expression = new SqlExpression('expression');
    }

	public function testConstruct() {
		$condition = new SimpleCondition($this->expression, $this->expression, Condition::OPERATOR_EQUALS);
		$this->assertEquals(Condition::OPERATOR_EQUALS, $condition->getOperator());
		$this->assertEquals($this->expression, $condition->getLeftExpression());
		$this->assertEquals($this->expression, $condition->getRightExpression());
	}

	/**
	 * @dataProvider providerConstructThrowsExceptionWhenInvalidOperatorPassed
	 * @expectedException zibo\library\database\exception\DatabaseException
	 */
	public function testConstructThrowsExceptionWhenInvalidOperatorPassed($operator) {
		new SimpleCondition($this->expression, $this->expression, $operator);
	}

	public function providerConstructThrowsExceptionWhenInvalidOperatorPassed() {
		return array(
            array(''),
            array(array()),
            array($this),
		);
	}

}