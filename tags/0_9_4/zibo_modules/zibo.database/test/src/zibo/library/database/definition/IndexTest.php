<?php

namespace zibo\library\database\definition;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class IndexTest extends BaseTestCase {

    public function testConstruct() {
        $name = 'index';
        $fields = array(
            'id' => new Field('id', 'integer'),
            'name' => new Field('name', 'string'),
        );
        $index = new Index($name, $fields);

        $this->assertEquals($name, Reflection::getProperty($index, 'name'));
        $this->assertEquals($fields, Reflection::getProperty($index, 'fields'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenEmptyNamePassed() {
        new Index('', array(new Field('id', 'integer')));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidNamePassed() {
        new Index($this, array(new Field('id', 'integer')));
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyFieldsPassed() {
        new Index('index', array());
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenInvalidFieldPassed() {
        new Index('index', array($this));
    }

}