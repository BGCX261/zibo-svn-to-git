<?php

namespace zibo\log;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \Exception;

class LogItemTest extends BaseTestCase {

    protected function setUp() {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    public function testConstruct() {
    	$title = 'title';
    	$message = 'message';
    	$type = LogItem::INFORMATION;
    	$name = 'test';
    	$date = time();
    	$ip = $_SERVER['REMOTE_ADDR'];

    	$logItem = new LogItem($title, $message, $type, $name);

    	$this->assertEquals($title, Reflection::getProperty($logItem, 'title'));
    	$this->assertEquals($message, Reflection::getProperty($logItem, 'message'));
    	$this->assertEquals($type, Reflection::getProperty($logItem, 'type'));
    	$this->assertEquals($name, Reflection::getProperty($logItem, 'name'));
    	$this->assertEquals($date, Reflection::getProperty($logItem, 'date'));
    	$this->assertEquals($ip, Reflection::getProperty($logItem, 'ip'));
    }

    public function testConstructedWithEmptyTitleThrowsException() {
        try {
            $item = new LogItem('');
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

    public function testSetTypeWithNullThrowsException() {
        try {
            $item = new LogItem('Test log title');
            $item->setType(null);
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

    public function testSetTypeWithInvalidTypeThrowsException() {
        try {
            $item = new LogItem('Test log title');
            $item->setType(-1);
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

}