<?php

namespace zibo\library\database\manipulation;

use zibo\library\database\driver\DriverMock;
use zibo\library\database\manipulation\condition\SimpleCondition;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\JoinExpression;
use zibo\library\database\manipulation\expression\OrderExpression;
use zibo\library\database\manipulation\expression\ScalarExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\statement\InsertStatement;
use zibo\library\database\manipulation\statement\SelectStatement;
use zibo\library\database\manipulation\statement\UpdateStatement;
use zibo\library\database\Dsn;

use zibo\test\BaseTestCase;

class GenericStatementParserTest extends BaseTestCase {

    private $parser;

    public function setUp() {
        $dsn = new Dsn('protocol://server/database');
        $connection = new DriverMock($dsn);
        $this->parser = new GenericStatementParser($connection);
    }

    public function testSelectStatement() {
        $statement = new SelectStatement();
        $statement->addField(new FieldExpression('field1'));
        $statement->addField(new FieldExpression('field2'));
        $statement->addField(new FieldExpression('field3'));
        $statement->addTable(new TableExpression('table'));

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('SELECT `field1`, `field2`, `field3` FROM `table`', $sql);
    }

    public function testSelectStatementWithTablesAndAliasses() {
        $table1 = new TableExpression('table1');
        $table2 = new TableExpression('table2_with_long_name', 'table2');

        $statement = new SelectStatement();
        $statement->addField(new FieldExpression('field1', $table1, 'f1'));
        $statement->addField(new FieldExpression('field1', $table2, 'f2'));
        $statement->addField(new FieldExpression('field2', null, 'field'));
        $statement->addTable($table1);
        $statement->addTable($table2);

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('SELECT `table1`.`field1` AS `f1`, `table2`.`field1` AS `f2`, `field2` AS `field` FROM `table1`, `table2_with_long_name` AS `table2`', $sql);
    }

    public function testSelectStatementWithCondition() {
        $table1 = new TableExpression('table1');
        $table2 = new TableExpression('table2_with_long_name', 'table2');

        $field = new FieldExpression('field1', $table2, 'f2');

        $condition = new SimpleCondition($field, new ScalarExpression('test'), '=');

        $statement = new SelectStatement();
        $statement->addField(new FieldExpression('field1', $table1, 'f1'));
        $statement->addField($field);
        $statement->addTable($table1);
        $statement->addTable($table2);
        $statement->addCondition($condition);

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('SELECT `table1`.`field1` AS `f1`, `table2`.`field1` AS `f2` FROM `table1`, `table2_with_long_name` AS `table2` WHERE `table2`.`field1` = \'test\'', $sql);
    }

    public function testSelectStatementWithTablesAndJoins() {
        $table1 = new TableExpression('table1', 't1');
        $table2 = new TableExpression('table2_with_long_name', 'table2');

        $joinCondition = new SimpleCondition(new FieldExpression('id', $table1), new FieldExpression('id_t1', $table2), '=');

        $table1->addJoin(new JoinExpression(JoinExpression::TYPE_INNER, $table2, $joinCondition));

        $statement = new SelectStatement();
        $statement->addField(new FieldExpression('field1', $table1, 'f1'));
        $statement->addTable($table1);

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('SELECT `t1`.`field1` AS `f1` FROM `table1` AS `t1` INNER JOIN `table2_with_long_name` AS `table2` ON `t1`.`id` = `table2`.`id_t1`', $sql);
    }

    public function testSelectStatementWithLimit() {
        $statement = new SelectStatement();
        $statement->addField(new FieldExpression('field1'));
        $statement->addField(new FieldExpression('field2'));
        $statement->addField(new FieldExpression('field3'));
        $statement->addTable(new TableExpression('table'));
        $statement->setLimit(15, 10);

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('SELECT `field1`, `field2`, `field3` FROM `table` LIMIT 15 OFFSET 10', $sql);
    }

    public function testSelectStatementWithOrder() {
        $table = new TableExpression('table', 't');
        $field1 = new FieldExpression('field1', $table, 'f1');
        $field2 = new FieldExpression('field2', $table);
        $field3 = new FieldExpression('field3');

        $statement = new SelectStatement();
        $statement->addField($field1);
        $statement->addField($field2);
        $statement->addField($field3);
        $statement->addTable($table);
        $statement->addOrderBy(new OrderExpression($field1));
        $statement->addOrderBy(new OrderExpression($field2, OrderExpression::DIRECTION_DESC));
        $statement->addOrderBy(new OrderExpression($field3));

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('SELECT `t`.`field1` AS `f1`, `t`.`field2`, `field3` FROM `table` AS `t` ORDER BY `f1` ASC, `t`.`field2` DESC, `field3` ASC', $sql);
    }

    public function testInsertStatement() {
        $statement = new InsertStatement();
        $statement->addTable(new TableExpression('table'));
        $statement->addValue(new FieldExpression('field1'), new ScalarExpression('test'));
        $statement->addValue(new FieldExpression('field2'), new ScalarExpression(2));
        $statement->addValue(new FieldExpression('field3'), new ScalarExpression(false));

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('INSERT INTO `table` (`field1`, `field2`, `field3`) VALUES (\'test\', 2, 0)', $sql);
    }

    public function testUpdateStatement() {
        $condition = new SimpleCondition(new FieldExpression('id'), new ScalarExpression(1), '=');

        $statement = new UpdateStatement();
        $statement->addTable(new TableExpression('table'));
        $statement->addValue(new FieldExpression('field1'), new ScalarExpression('test'));
        $statement->addValue(new FieldExpression('field2'), new ScalarExpression(2));
        $statement->addValue(new FieldExpression('field3'), new ScalarExpression(false));

        $statement->addCondition($condition);

        $sql = $this->parser->parseStatement($statement);
        $this->assertNotNull($sql);
        $this->assertEquals('UPDATE `table` SET `field1` = \'test\', `field2` = 2, `field3` = 0 WHERE `id` = 1', $sql);
    }

}