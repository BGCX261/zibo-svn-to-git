<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\manipulation\expression\TableExpression;

use zibo\test\BaseTestCase;

class DeleteStatementTest extends BaseTestCase {

	private $statement;

	public function setUp() {
		$this->statement = new DeleteStatement();
	}

	public function testAddTable() {
		$table = new TableExpression('table');

		$this->statement->addTable($table);

		$tables = $this->statement->getTables();

		$expected = array('table' => $table);

		$this->assertEquals($expected, $tables);
	}

	/**
	 * @expectedException zibo\library\database\exception\DatabaseException
	 */
	public function testAddTableThrowsExceptionWhenAlreadyAddedTable() {
		$table = new TableExpression('table');

		$this->statement->addTable($table);
		$this->statement->addTable($table);
	}

}