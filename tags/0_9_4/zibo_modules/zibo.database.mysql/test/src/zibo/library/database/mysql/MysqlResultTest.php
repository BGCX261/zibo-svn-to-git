<?php

namespace zibo\library\database\mysql;

use zibo\test\BaseTestCase;

class MysqlResultTest extends BaseTestCase {

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenInvalidResultPassed() {
        new MysqlResult('sql', 'invalid');
    }

}