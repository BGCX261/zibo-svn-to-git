<?php

namespace zibo\library\database\definition;

use zibo\test\BaseTestCase;

class ForeignKeyTest extends BaseTestCase {

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyFieldNamePassed() {
        new ForeignKey('', 'table', 'id');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyReferenceTablePassed() {
        new ForeignKey('field', '', 'id');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyReferenceFieldNamePassed() {
        new ForeignKey('field', 'table', '');
    }

}