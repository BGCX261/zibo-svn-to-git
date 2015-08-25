<?php

namespace zibo\library\database\manipulation\expression;

use zibo\test\BaseTestCase;

class FieldExpressionTest extends BaseTestCase {

    public function testConstruct() {
        $field = new FieldExpression('field');

        $this->assertEquals('field', $field->getName());
        $this->assertNull($field->getTable());
        $this->assertNull($field->getAlias());
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenNameIsEmpty() {
        new FieldExpression('');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenAliasIsEmpty() {
        new FieldExpression('field', null, '');
    }

}