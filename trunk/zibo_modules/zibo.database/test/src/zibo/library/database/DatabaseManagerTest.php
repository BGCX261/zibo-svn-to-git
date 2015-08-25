<?php

namespace zibo\library\database;

use zibo\core\filesystem\GenericFileBrowser;
use zibo\core\Zibo;

use zibo\library\database\driver\Driver;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class DatabaseManagerTest extends BaseTestCase {

	const DRIVER_MOCK = 'zibo\\library\\database\\driver\\DriverMock';

    private $configIOMock;

    protected function setUp() {
        $browser = new GenericFileBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();

        $this->zibo = new Zibo($browser, $this->configIOMock);
    }

    public function testConstructLoadsDrivers() {
    	$config = array(
            'driver' => array(
                'protocol' => self::DRIVER_MOCK,
            ),
        );
        $this->configIOMock->setValues('database', $config);

        $manager = new DatabaseManager($this->zibo);

        $drivers = Reflection::getProperty($manager, 'drivers');

        $this->assertTrue(is_array($drivers));
        $this->assertEquals($config['driver'], $drivers);
    }

    public function testConstructLoadsConnections() {
    	$name = 'name';
    	$config = array(
            'driver' => array(
                'protocol' => self::DRIVER_MOCK,
            ),
            'connection' => array(
                $name => 'protocol://server/database',
            ),
        );
        $this->configIOMock->setValues('database', $config);

        $manager = new DatabaseManager($this->zibo);

        $connections = Reflection::getProperty($manager, 'connections');

        $this->assertTrue(is_array($connections));
        $this->assertNotNull($connections[$name]);
    }

    public function testConstructHasDrivers() {
        $manager = new DatabaseManager($this->zibo);

        $drivers = Reflection::getProperty($manager, 'drivers');

        $this->assertTrue(is_array($drivers));
    }

    public function testConstructHasConnections() {
        $manager = new DatabaseManager($this->zibo);

        $connections = Reflection::getProperty($manager, 'connections');

        $this->assertTrue(is_array($connections));
    }

    public function testRegisterDriver() {
        $manager = new DatabaseManager($this->zibo);
        $manager->registerDriver('protocol', self::DRIVER_MOCK);

        $drivers = Reflection::getProperty($manager, 'drivers');

        $this->assertTrue(in_array(self::DRIVER_MOCK, $drivers));
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterDriverThrowsExceptionWithEmptyProtocol() {
        $manager = new DatabaseManager($this->zibo);
        $manager->registerDriver('', self::DRIVER_MOCK);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterDriverThrowsExceptionWithEmptyDriver() {
        $manager = new DatabaseManager($this->zibo);
        $manager->registerDriver('protocol', '');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterDriverThrowsExceptionWithInvalidDriver() {
        $driver = 'zibo\\library\\database\\DatabaseManager';
        $manager = new DatabaseManager($this->zibo);
        $manager->registerDriver('invalid', $driver);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testGetConnectionThrowsExceptionWhenConnectionNotFound() {
    	$manager = new DatabaseManager($this->zibo);
        $manager->getConnection();
    }

    public function testRegisterConnection() {
    	$manager = new DatabaseManager($this->zibo);

    	$manager->registerDriver('protocol', self::DRIVER_MOCK);

        $connectionName = 'test';
        $manager->registerConnection($connectionName, new Dsn('protocol://server/database'));

        $connections = Reflection::getProperty($manager, 'connections');
        $this->assertArrayHasKey($connectionName, $connections);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterConnectionThrowsExceptionWhenNameIsEmpty() {
    	$manager = new DatabaseManager($this->zibo);
        $manager->registerConnection('', new Dsn('protocol://server/database'));
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterConnectionThrowsExceptionWhenProtocolHasNoDriver() {
    	$manager = new DatabaseManager($this->zibo);
        $dsn = new Dsn('protocol://server/database');
        $manager->registerConnection('test', $dsn);
    }

    public function testGetConnectionWithConnectionName() {
    	$manager = new DatabaseManager($this->zibo);

    	$manager->registerDriver('protocol', self::DRIVER_MOCK);

        $connectionName = 'test';
        $dsn = new Dsn('protocol://server/database');
        $manager->registerConnection($connectionName, $dsn);

        $connection = $manager->getConnection($connectionName);
        $this->assertTrue($connection instanceof Driver, 'connection is not a Driver');
        $this->assertTrue($connection->isConnected(), 'connection is not connected');
    }

    public function testGetConnectionWithoutConnectionName() {
    	$manager = new DatabaseManager($this->zibo);

        $manager->registerDriver('protocol', self::DRIVER_MOCK);

        $connectionName = 'test';
        $dsn = new Dsn('protocol://server/database');
        $manager->registerConnection($connectionName, $dsn);

        $connection = $manager->getConnection();
        $this->assertTrue($connection instanceof Driver, 'connection is not a Driver');
        $this->assertTrue($connection->isConnected(), 'connection is not connected');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testSetDefaultConnectionThrowsExceptionWhenNameIsEmpty() {
    	$manager = new DatabaseManager($this->zibo);
        $manager->setDefaultConnectionName('');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testSetDefaultConnectionThrowsExceptionWhenNameDoesNotExist() {
    	$manager = new DatabaseManager($this->zibo);
        $manager->setDefaultConnectionName('unexistant');
    }

}