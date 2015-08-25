<?php

namespace zibo\library;

use zibo\core\Zibo;


use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

use \Exception;

class SessionTest extends BaseTestCase {

    private $sessionGcProbability = 2;
    private $sessionGcDivisor = 100;
    private $sessionPath = 'application/data/session';
    private $sessionTime = 60;

    public function __construct() {
        @session_start();
    }

    public function setUp() {
        $browser = $this->getMock('zibo\\library\\filesystem\\browser\\Browser');
        $configIOMock =  new ConfigIOMock();
        $systemConfigValues = array(
            'session' => array(
                'gc' => array(
                    'probability' => $this->sessionGcProbability,
                    'divisor' => $this->sessionGcDivisor,
                 ),
                'path' => $this->sessionPath,
                'time' => $this->sessionTime,
            ),
        );
        $configIOMock->setValues('system', $systemConfigValues);

        Zibo::getInstance($browser, $configIOMock);
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
        Reflection::setProperty(Session::getInstance(), 'instance', null);
    }

    public function testPhpIniSettingsAreSetFromSystemSessionConfig() {
        $session = Session::getInstance();

        $this->assertEquals($this->sessionTime * 60, ini_get('session.gc_maxlifetime'));
        $this->assertEquals($this->sessionGcProbability, ini_get('session.gc_probability'));
        $this->assertEquals($this->sessionGcDivisor, ini_get('session.gc_divisor'));
        $this->assertEquals($this->sessionPath, ini_get('session.save_path'));
    }

    public function testSet() {
        $key = 'key';
        $value = 'value';

        $session = Session::getInstance();
        $session->set($key, $value);
        $result = $session->get($key);

        $this->assertEquals($value, $result);
    }

    public function testUnset() {
        $key = 'key';

        $session = Session::getInstance();
        $session->set($key);
        $result = $session->get($key);

        $this->assertNull($result);
    }

    public function testGetWithDefault() {
        $key = 'key2';
        $default = 'default';

        $session = Session::getInstance();
        $session->set($key);
        $result = $session->get($key, $default);

        $this->assertEquals($default, $result);
    }

    public function testReset() {
        $session = Session::getInstance();
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');

        $sessionKey = Session::SESSION_NAME;

        $this->assertTrue(count($_SESSION[$sessionKey]) >= 2, 'session array has not at least the added elements');

        $session->reset();

        $this->assertTrue(isset($_SESSION[$sessionKey]), 'session array does not exist');
        $this->assertTrue(count($_SESSION[$sessionKey]) == 0, 'session array is not empty');
    }

}