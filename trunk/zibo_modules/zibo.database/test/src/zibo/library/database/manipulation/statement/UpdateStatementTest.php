<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\expression\ValueExpression;

use zibo\test\BaseTestCase;

class UpdateStatementTest extends BaseTestCase {

	private $statement;

	public function setUp() {
		$this->statement = new UpdateStatement();
	}

    public function testAddTable() {
        $table = new TableExpression('table');

        $this->statement->addTable($table);

        $tables = $this->statement->getTables();

        $expected = array('table' => $table);

        $this->assertEquals($expected, $tables);
    }

    public function testAddValue() {
        $field1 = new FieldExpression('field1');
        $value1 = 'value1';
        $field2 = new FieldExpression('field2');
        $value2 = 'value2';

        $this->statement->addValue($field1, $value1);
        $this->statement->addValue($field2, $value2);

        $values = $this->statement->getValues();

        $expected = array(
            new ValueExpression($field1, $value1),
            new ValueExpression($field2, $value2)
        );

        $this->assertEquals($expected, $values);
    }

}