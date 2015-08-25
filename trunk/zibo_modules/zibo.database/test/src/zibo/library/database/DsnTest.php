<?php

namespace zibo\library\database;

use zibo\test\BaseTestCase;

class DsnTest extends BaseTestCase {

    public function testConstructWithMysqlDatabase() {
        $dsn = new Dsn('mysql://username:password@localhost:3306/database');
        $this->assertEquals('mysql', $dsn->getProtocol());
        $this->assertEquals('localhost', $dsn->getHost());
        $this->assertEquals('3306', $dsn->getPort());
        $this->assertEquals('username', $dsn->getUsername());
        $this->assertEquals('password', $dsn->getPassword());
        $this->assertEquals('database', $dsn->getDatabase());
    }

    public function testConstructWithSqliteDatabase() {
        $dsn = new Dsn('sqlite:///var/lib/sqlite/file.db');
        $this->assertEquals('sqlite', $dsn->getProtocol());
        $this->assertEquals('', $dsn->getHost());
        $this->assertEquals('', $dsn->getPort());
        $this->assertEquals('', $dsn->getUsername());
        $this->assertEquals('', $dsn->getPassword());
        $this->assertEquals('file.db', $dsn->getDatabase());
        $this->assertEquals('/var/lib/sqlite/file.db', $dsn->getPath());
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidDsnPassed
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenInvalidDsnPassed($dsn) {
        new Dsn($dsn);
    }

    public function providerConstructThrowsExceptionWhenInvalidDsnPassed() {
        return array(
            array(''),
            array('test'),
        );
    }

}