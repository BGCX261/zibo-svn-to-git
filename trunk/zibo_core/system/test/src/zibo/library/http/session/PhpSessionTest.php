<?php

namespace zibo\library\http\session;

use zibo\core\Zibo;


use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

use \Exception;

class PhpSessionTest extends BaseTestCase {

    private $session;

    public function setUp() {
        $this->session = new PhpSession();
    }

    public function testSet() {
        $key = 'key';
        $value = 'value';

        $this->session->set($key, $value);
        $result = $this->session->get($key);

        $this->assertEquals($value, $result);
    }

    public function testUnset() {
        $key = 'key';

        $this->session->set($key);
        $result = $this->session->get($key);

        $this->assertNull($result);
    }

    public function testGetWithDefault() {
        $key = 'key2';
        $default = 'default';

        $this->session->set($key);
        $result = $this->session->get($key, $default);

        $this->assertEquals($default, $result);
    }

    public function testReset() {
        $this->session->set('key1', 'value1');
        $this->session->set('key2', 'value2');

        $this->session->reset();

        $this->assertTrue(count($_SESSION) == 0, 'session array is not empty');
    }

}