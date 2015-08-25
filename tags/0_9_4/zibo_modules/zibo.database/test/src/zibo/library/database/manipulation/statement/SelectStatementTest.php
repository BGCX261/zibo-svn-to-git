<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\manipulation\expression\TableExpression;

use zibo\test\BaseTestCase;

class SelectStatementTest extends BaseTestCase {

	private $statement;

	public function setUp() {
		$this->statement = new SelectStatement();
	}

    public function testAddTable() {
        $table1 = new TableExpression('tableName');
        $table2 = new TableExpression('tableName2', 'table2Alias');

        $this->statement->addTable($table1);
        $this->statement->addTable($table2);

        $tables = $this->statement->getTables();

        $expected = array(
            'tableName' => $table1,
            'table2Alias' => $table2,
        );

        $this->assertEquals($expected, $tables);
    }

	/**
	 * @expectedException zibo\ZiboException
	 */
	public function testSetDistinctThrowsExceptionWhenNoBooleanProvided() {
        $this->statement->setDistinct('test');
	}

	/**
     * @dataProvider providerSetLimitThrowsExceptionWhenInvalidValueProvided
     * @expectedException zibo\ZiboException
	 */
	public function testSetLimitThrowsExceptionWhenInvalidValueProvided($count, $offset) {
        $this->statement->setLimit($count, $offset);
	}

	public function providerSetLimitThrowsExceptionWhenInvalidValueProvided() {
		return array(
            array(-15, 0),
            array('test', 0),
            array(15, 'test'),
            array(15, -15),
		);
	}

}