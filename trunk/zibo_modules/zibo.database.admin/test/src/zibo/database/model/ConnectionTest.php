<?php

namespace zibo\database\model;

use zibo\library\database\DriverMock;
use zibo\library\database\Dsn;

use zibo\test\BaseTestCase;

class ConnectionTest extends BaseTestCase {

	/**
	 * @dataProvider providerConstructThrowsExceptionWhenInvalidNamePassed
     * @expectedException zibo\ZiboException
	 */
	public function testConstructThrowsExceptionWhenInvalidNamePassed($name) {
        $driver = new DriverMock(new Dsn('protocol://server/database'));
        new Connection($name, $driver);
	}

	public function providerConstructThrowsExceptionWhenInvalidNamePassed() {
        return array(
            array(''),
            array($this),
        );
	}

}