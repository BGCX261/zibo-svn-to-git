<?php

namespace zibo\library\database\definition;

use zibo\test\BaseTestCase;

class FieldTest extends BaseTestCase {

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyNamePassed() {
        new Field('', 'type');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyTypePassed() {
        new Field('name', '');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testSetIsAutoNumberingThrowsExceptionWhenNoBooleanPassed() {
        $field = new Field('name', 'type');
        $field->setIsAutoNumbering('test');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testSetIsPrimaryKeyThrowsExceptionWhenNoBooleanPassed() {
        $field = new Field('name', 'type');
        $field->setIsPrimaryKey('test');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testSetIsIndexedThrowsExceptionWhenNoBooleanPassed() {
        $field = new Field('name', 'type');
        $field->setIsIndexed('test');
    }

}