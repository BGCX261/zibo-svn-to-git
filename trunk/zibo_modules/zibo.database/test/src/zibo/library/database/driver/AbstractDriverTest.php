<?php

namespace zibo\library\database\driver;

use zibo\library\database\Dsn;
use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

use \Exception;

class AbstractDriverTest extends BaseTestCase {

    private $driver;

    protected function setUp() {
        $dsn = new Dsn('mysql://localhost/database');
        $this->driver = $this->getMock('zibo\\library\\database\\driver\\AbstractDriver', array('isConnected', 'connect', 'disconnect', 'ping', 'execute', 'getLastInsertId'), array($dsn));
    }

    /**
     * @dataProvider providerQuoteIdentifierThrowsDatabaseExceptionWhenInvalidValuePassed
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testQuoteIdentifierThrowsDatabaseExceptionWhenInvalidValuePassed($value) {
        $this->driver->quoteIdentifier($value);
    }

    public function providerQuoteIdentifierThrowsDatabaseExceptionWhenInvalidValuePassed() {
    	return array(
            array(array()),
            array(''),
            array($this),
    	);
    }

    /**
     * @dataProvider providerQuoteValueThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testQuoteValueThrowsExceptionWhenInvalidValuePassed($value) {
        $this->driver->quoteValue($value);
    }

    public function providerQuoteValueThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array(array('element1')),
            array($this),
        );
    }

}