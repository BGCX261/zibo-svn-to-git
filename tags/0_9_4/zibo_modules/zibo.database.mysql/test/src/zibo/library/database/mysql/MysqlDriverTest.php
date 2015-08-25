<?php

namespace zibo\library\database\mysql;

use zibo\library\database\definition\definer\Definer;
use zibo\library\database\definition\Field;
use zibo\library\database\definition\Table;
use zibo\library\database\DatabaseResult;
use zibo\library\database\Dsn;

class MysqlDriverTest extends MysqlTestCase {

    public function testConstructHasDefiner() {
        $connection = $this->getConnection(false);
        $definer = $connection->getDefiner();

        $this->assertTrue($definer instanceof Definer);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenDsnHasInvalidProtocol() {
        new MysqlDriver(new Dsn('postgress://localhost/test'));
    }

    public function testConnectAndDisconnect() {
        $connection = $this->getConnection(false);

        $isConnected = $connection->isConnected();
        $this->assertFalse($isConnected, 'Connection is already connected');

        $connection->connect();

        $isConnected = $connection->isConnected();
        $this->assertTrue($isConnected, 'Didn\'t connect');

        $connection->disconnect();

        $isConnected = $connection->isConnected();
        $this->assertFalse($isConnected, 'Connection didn\'t disconnect');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConnectThrowsExceptionWhenConnectionNotConnectable() {
        $connection = new MysqlDriver(new Dsn('mysql://localhost/unexistant'));
        $connection->connect();
    }

    public function testExecute() {
        $connection = $this->getConnection();
        $result = $connection->execute('SHOW DATABASES');

        $this->assertTrue($result instanceof DatabaseResult, 'Not a result instance');
        $this->assertNotEquals(0, $result->getRowCount(), 'Result has no rows');
        $this->assertNotEquals(0, $result->getColumnCount(), 'Result has no columns');

        $found = false;
        foreach ($result as $record) {
            if ($record['Database'] == 'information_schema') {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Didn\'t find expected result');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testExecuteThrowsExceptionWhenSqlIsEmpty() {
        $connection = $this->getConnection();
        $connection->execute('');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testExecuteThrowsExceptionWhenNotConnected() {
        $connection = $this->getConnection(false);
        $connection->execute('SHOW DATABASES');
    }

}