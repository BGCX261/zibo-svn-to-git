<?php

namespace zibo\library\database\mysql;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\database\Dsn;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\ZiboException;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

abstract class MysqlTestCase extends BaseTestCase {

    protected $dsn;

    public function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new IniConfigIO(Environment::getInstance(), $browser);

        $zibo = Zibo::getInstance($browser, $configIO);
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    protected function getConnection($connect = true) {
        $dsn = Zibo::getInstance()->getConfigValue('database.connection.mysql');

        if ($dsn == null) {
            Reflection::setProperty(Zibo::getInstance(), 'instance', null);
            $this->markTestSkipped('No dsn found for database.connection.mysql, check config/database.ini');
            return;
        }

        $this->dsn = new Dsn($dsn);

        $connection = new MysqlDriver($this->dsn);

        if ($connect) {
            $connection->connect();
        }

        return $connection;
    }

}