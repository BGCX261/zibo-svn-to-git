<?php

namespace zibo\library\database\manipulation\condition;

use zibo\test\BaseTestCase;

class SqlConditionTest extends BaseTestCase {

	/**
	 * @dataProvider providerConstructThrowsExceptionWhenSqlIsInvalid
	 * @expectedException zibo\library\database\exception\DatabaseException
	 */
	public function testConstructThrowsExceptionWhenSqlIsInvalid($value) {
		new SqlCondition($value);
	}

	public function providerConstructThrowsExceptionWhenSqlIsInvalid() {
		return array(
            array(''),
            array($this),
		);
	}

}