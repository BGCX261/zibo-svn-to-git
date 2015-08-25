<?php

namespace zibo\library\database\manipulation\condition;

use zibo\library\database\manipulation\expression\SqlExpression;

use zibo\test\BaseTestCase;

class NestedConditionTest extends BaseTestCase {

    private $condition;

    public function setUp() {
        $this->condition = new SimpleCondition(new SqlExpression('left'), new SqlExpression('right'), Condition::OPERATOR_EQUALS);
    }

	public function testConstruct() {
		$condition = new NestedCondition();
		$this->assertEquals(array(), $condition->getParts());
	}

	/**
	 * @dataProvider providerAddConditionThrowsExceptionWhenInvalidOperatorPassed
	 * @expectedException zibo\ZiboException
	 */
	public function testAddConditionThrowsExceptionWhenInvalidOperatorPassed($operator) {
		$condition = new NestedCondition();
		$condition->addCondition($this->condition, $operator);
	}

	public function providerAddConditionThrowsExceptionWhenInvalidOperatorPassed() {
		return array(
            array(''),
            array('test'),
            array($this),
		);
	}

}