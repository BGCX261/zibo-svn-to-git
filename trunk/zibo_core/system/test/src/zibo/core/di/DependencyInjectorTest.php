<?php

namespace zibo\core\di;

use zibo\library\String;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DependencyInjectorTest extends BaseTestCase {

    private $di;

    public function setUp() {
        $this->di = new DependencyInjector();
    }

    public function tearDown() {
        Reflection::setProperty($this->di, 'container', null);
        Reflection::setProperty($this->di, 'instances', null);
    }

    public function testConstruct() {
        $this->assertNull(Reflection::getProperty($this->di, 'container'));
        $this->assertNull(Reflection::getProperty($this->di, 'instances'));
    }

    public function testSetContainer() {
        $container = new DependencyContainer();

        Reflection::setProperty($this->di, 'instances', array($this));

        $this->di->setContainer($container);

        $this->assertEquals($container, Reflection::getProperty($this->di, 'container'));
        $this->assertNull(Reflection::getProperty($this->di, 'instances'));
    }

    public function testGetContainer() {
        $this->assertNull(Reflection::getProperty($this->di, 'container'));

        $container = $this->di->getContainer();

        $this->assertNotNull($container);
        $this->assertEquals($container, Reflection::getProperty($this->di, 'container'));

        $container2 = new DependencyContainer();
        $container2->addDependency('for', new Dependency('className'));

        $this->di->setContainer($container2);

        $this->assertEquals($container2, Reflection::getProperty($this->di, 'container'));
    }

    public function testGet() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $dependency = new Dependency('zibo\\core\\di\\TestObject');

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency);

        $this->di->setContainer($container);

        $instance = $this->di->get($interface);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);

        $token = $instance->getToken();

        $instance = $this->di->get($interface);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);
        $this->assertEquals($token, $instance->getToken()); // it's the same instance
    }

    public function testGetUsesTheLastDefinedDependency() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $dependency = new Dependency('zibo\\core\\di\\TestObject');

        $container = new DependencyContainer();
        $container->addDependency($interface, new Dependency('zibo\\core\\di\\Dummy'));
        $container->addDependency($interface, $dependency);

        $this->di->setContainer($container);

        $instance = $this->di->get($interface);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);
    }

    public function testGetWithId() {
        $interface = 'zibo\\core\\di\\TestInterface';
        $id = 'id';

        $dependency = new Dependency('zibo\\core\\di\\TestObject');
        $dependency->setId($id);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency);
        $container->addDependency($interface, new Dependency('zibo\\core\\di\\Dummy'));

        $this->di->setContainer($container);

        $instance = $this->di->get($interface, $id);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);
    }

    public function testGetCallsConstructor() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $token = 'test';

        $construct = new DependencyCall('__construct');
        $construct->addArgument(new DependencyCallArgument('value', $token));

        $dependency = new Dependency('zibo\\core\\di\\TestObject');
        $dependency->addCall($construct);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency);

        $this->di->setContainer($container);

        $instance = $this->di->get($interface);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);
        $this->assertEquals($token, $instance->getToken());
    }

    public function testGetCallsMethods() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $token1 = 'test1';
        $token2 = 'test2';

        $method1 = new DependencyCall('setToken');
        $method1->addArgument(new DependencyCallArgument('value', $token1));
        $method2 = new DependencyCall('setToken');
        $method2->addArgument(new DependencyCallArgument('value', $token2));

        $dependency = new Dependency('zibo\\core\\di\\TestObject');
        $dependency->addCall($method1);
        $dependency->addCall($method2);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency);

        $this->di->setContainer($container);

        $instance = $this->di->get($interface);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);

        $this->assertEquals($token2, $instance->getToken());

        $history = $instance->getTokenHistory();
        $this->assertEquals(2, count($history));
        $this->assertEquals($token1, $history[1]);
    }

    public function testGetInjectsDependencies() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $token = 'test';

        $construct = new DependencyCall('__construct');
        $construct->addArgument(new DependencyCallArgument('value', $token));
        $method1 = new DependencyCall('setTest');
        $method1->addArgument(new DependencyCallArgument('dependency', 'zibo\\core\\di\\TestInterface'));

        $dependency1 = new Dependency('zibo\\core\\di\\TestObject');
        $dependency1->addCall($construct);
        $dependency2 = new Dependency('zibo\\core\\di\\TestObject');
        $dependency2->addCall($method1);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency1);
        $container->addDependency($interface, $dependency2);

        $this->di->setContainer($container);

        $instance = $this->di->get($interface);

        $this->assertNotNull($instance);
        $this->assertTrue($instance instanceof TestObject);

        $test = $instance->getTest();

        $this->assertNotNull($test);
        $this->assertEquals($token, $test->getToken());
    }

    public function testSetInstance() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $instance1 = new TestObject();
        $instance2 = new TestObject();

        $this->di->setInstance($instance1);
        $this->di->setInstance($instance2, $interface);

        $expected = array(
            'zibo\\core\\di\\TestObject' => $instance1,
            $interface => $instance2,
        );

        $this->assertEquals($expected, Reflection::getProperty($this->di, 'instances'));
    }

    public function testGetWithSettedInstance() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $token = 'test';

        $dependency = new Dependency('zibo\\core\\di\\TestObject');
        $instance = new TestObject($token);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency);

        $this->di->setContainer($container);

        $result = $this->di->get($interface);

        $this->assertNotNull($result);
        $this->assertTrue($result instanceof $interface);
        $this->assertNotEquals($token, $result->getToken());

        $this->di->setInstance($instance, $interface);

        $result = $this->di->get($interface);

        $this->assertEquals($instance, $result);
    }

    public function testGetOnAllInstancesOfDepencyInjectorYieldTheSameResults() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $token = 'test';

        $construct = new DependencyCall('__construct');
        $construct->addArgument(new DependencyCallArgument('value', $token));

        $dependency = new Dependency('zibo\\core\\di\\TestObject');
        $dependency->addCall($construct);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency);

        $this->di->setContainer($container);

        for ($i = 0; $i < 3; $i++) {
            $di = new DependencyInjector();
            $instance = $di->get($interface);

            $this->assertEquals($token, $instance->getToken());
        }
    }

    public function testGetAll() {
        $interface = 'zibo\\core\\di\\TestInterface';

        $token1 = 'test1';
        $token2 = 'test2';
        $id = 'id';

        $construct1 = new DependencyCall('__construct');
        $construct1->addArgument(new DependencyCallArgument('value', $token1));
        $construct2 = new DependencyCall('__construct');
        $construct2->addArgument(new DependencyCallArgument('value', $token2));

        $dependency1 = new Dependency('zibo\\core\\di\\TestObject', $id);
        $dependency1->addCall($construct1);
        $dependency2 = new Dependency('zibo\\core\\di\\TestObject');
        $dependency2->addCall($construct2);

        $container = new DependencyContainer();
        $container->addDependency($interface, $dependency1);
        $container->addDependency($interface, $dependency2);

        $this->di->setContainer($container);

        $expected = array(
            $id => new TestObject($token1),
            0 => new TestObject($token2),
        );

        $result = $this->di->getAll($interface);

        $this->assertEquals($expected, $result);
    }

}

interface TestInterface {

    public function method();

}

class TestObject implements TestInterface {

    private $token;

    private $tokenHistory = array();

    private $test;

    public function __construct($token = null) {
        if ($token) {
            $this->setToken($token);
        } else {
            $this->setToken(String::generate(5));
        }
    }

    public function setToken($token) {
        if ($this->token) {
            $this->tokenHistory[] = $this->token;
        }

        $this->token = $token;
    }

    public function getToken() {
        return $this->token;
    }

    public function getTokenHistory() {
        return $this->tokenHistory;
    }

    public function setTest(TestInterface $test) {
        $this->test = $test;
    }

    public function getTest() {
        return $this->test;
    }

    public function method() {

    }

}