<?php

namespace zibo\library\database\mysql;

use zibo\library\database\definition\Field;
use zibo\library\database\definition\Table;
use zibo\library\database\query\Result;
use zibo\library\database\Dsn;

class MysqlDefinerTest extends MysqlTestCase {

    public function setUp() {
        parent::setUp();

        $this->connection = $this->getConnection();
        $this->definer = $this->connection->getDefiner();

        $this->field1 = new Field('id', 'pk');
        $this->field1->setIsPrimaryKey(true);
        $this->field1->setIsAutoNumbering(true);
        $this->field2 = new Field('name', 'string');

        $this->table = new Table('test table');
        $this->table->addField($this->field1);
        $this->table->addField($this->field2);
    }

    public function tearDown() {
        $this->connection->execute('DROP TABLE IF EXISTS `' . $this->table->getName() . '`');
        parent::tearDown();
    }

    /**
     * @dataProvider providerTableExists
     */
    public function testTableExists($expected, $tableName) {
        $result = $this->definer->tableExists($tableName);
        $this->assertEquals($expected, $result, $tableName);
    }

    public function providerTableExists() {
        return array(
            array(true, 'existant'),
            array(false, 'unexistant'),
        );
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testTableExistsThrowExceptionWhenTableNameIsEmpty() {
        $this->definer->tableExists('');
    }

    public function testCreateAndDropTable() {
        $this->definer->defineTable($this->table);

        $this->assertTrue($this->definer->tableExists($this->table->getName()), 'Didn\'t create the table');

        $this->definer->dropTable($this->table->getName());

        $this->assertFalse($this->definer->tableExists($this->table->getName()), 'Table still exists');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testDropTableThrowsExceptionWhenTableNameIsEmtpy() {
        $this->definer->dropTable('');
    }

    public function testAlterTable() {
        if ($this->definer->tableExists($this->table->getName())) {
            $this->definer->dropTable($this->table->getName());
        }

        $this->definer->defineTable($this->table);

        $this->table->addField(new Field('description', 'string'));

        $this->definer->defineTable($this->table);

        $this->assertTrue($this->definer->tableExists($this->table->getName()), 'Table is dropped');
    }

}