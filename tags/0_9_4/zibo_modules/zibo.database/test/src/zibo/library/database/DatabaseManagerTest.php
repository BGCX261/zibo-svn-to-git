<?php

namespace zibo\library\database;

use zibo\core\Zibo;

use zibo\library\database\driver\Driver;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class DatabaseManagerTest extends BaseTestCase {

	const DRIVER_MOCK = 'zibo\\library\\database\\driver\\DriverMock';

    private $configIOMock;

    protected function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();

        $zibo = Zibo::getInstance($browser, $this->configIOMock);
    }

    protected function tearDown() {
        Reflection::setProperty(DatabaseManager::getInstance(), 'instance', null);
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testGetInstanceLoadsDrivers() {
    	$config = array(
            'driver' => array(
                'protocol' => self::DRIVER_MOCK,
            ),
        );
        $this->configIOMock->setValues('database', $config);

        $manager = DatabaseManager::getInstance();
        $drivers = Reflection::getProperty($manager, 'drivers');
        $this->assertTrue(is_array($drivers));
        $this->assertEquals($config['driver'], $drivers);
    }

    public function testGetInstanceLoadsConnections() {
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

        $manager = DatabaseManager::getInstance();
        $connections = Reflection::getProperty($manager, 'connections');
        $this->assertTrue(is_array($connections));
        $this->assertNotNull($connections[$name]);
    }

    public function testGetInstanceHasDrivers() {
        $manager = DatabaseManager::getInstance();
        $drivers = Reflection::getProperty($manager, 'drivers');
        $this->assertTrue(is_array($drivers));
    }

    public function testGetInstanceHasConnections() {
        $manager = DatabaseManager::getInstance();
        $connections = Reflection::getProperty($manager, 'connections');
        $this->assertTrue(is_array($connections));
    }

    public function testRegisterDriver() {
        $manager = DatabaseManager::getInstance();
        $manager->registerDriver('protocol', self::DRIVER_MOCK);
        $drivers = Reflection::getProperty($manager, 'drivers');
        $this->assertTrue(in_array(self::DRIVER_MOCK, $drivers));
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterDriverThrowsExceptionWithEmptyProtocol() {
        $manager = DatabaseManager::getInstance();
        $manager->registerDriver('', self::DRIVER_MOCK);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterDriverThrowsExceptionWithEmptyDriver() {
        $manager = DatabaseManager::getInstance();
        $manager->registerDriver('protocol', '');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterDriverThrowsExceptionWithInvalidDriver() {
        $driver = 'zibo\\library\\database\\DatabaseManager';
        $manager = DatabaseManager::getInstance();
        $manager->registerDriver('invalid', $driver);
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testGetConnectionThrowsExceptionWhenConnectionNotFound() {
    	$manager = DatabaseManager::getInstance();
        $manager->getConnection();
    }

    public function testRegisterConnection() {
    	$manager = DatabaseManager::getInstance();

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
    	$manager = DatabaseManager::getInstance();
        $manager->registerConnection('', new Dsn('protocol://server/database'));
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testRegisterConnectionThrowsExceptionWhenProtocolHasNoDriver() {
    	$manager = DatabaseManager::getInstance();
        $dsn = new Dsn('protocol://server/database');
        $manager->registerConnection('test', $dsn);
    }

    public function testGetConnectionWithConnectionName() {
    	$manager = DatabaseManager::getInstance();

    	$manager->registerDriver('protocol', self::DRIVER_MOCK);

        $connectionName = 'test';
        $dsn = new Dsn('protocol://server/database');
        $manager->registerConnection($connectionName, $dsn);

        $connection = $manager->getConnection($connectionName);
        $this->assertTrue($connection instanceof Driver, 'connection is not a Driver');
        $this->assertTrue($connection->isConnected(), 'connection is not connected');
    }

    public function testGetConnectionWithoutConnectionName() {
    	$manager = DatabaseManager::getInstance();

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
    	$manager = DatabaseManager::getInstance();
        $manager->setDefaultConnectionName('');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testSetDefaultConnectionThrowsExceptionWhenNameDoesNotExist() {
    	$manager = DatabaseManager::getInstance();
        $manager->setDefaultConnectionName('unexistant');
    }

}