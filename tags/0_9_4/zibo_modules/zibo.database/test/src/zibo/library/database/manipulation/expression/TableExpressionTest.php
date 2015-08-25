<?php

namespace zibo\library\database\manipulation\expression;

use zibo\test\BaseTestCase;

class TableExpressionTest extends BaseTestCase {

    public function testConstruct() {
        $table = new TableExpression('table');

        $this->assertEquals('table', $table->getName());
        $this->assertNull($table->getAlias());
    }

    public function testConstructWithAlias() {
        $table = new TableExpression('table', 'alias');

        $this->assertEquals('table', $table->getName());
        $this->assertEquals('alias', $table->getAlias());
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenNameIsEmpty() {
        new TableExpression('');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenAliasIsEmpty() {
        new TableExpression('table', '');
    }

}