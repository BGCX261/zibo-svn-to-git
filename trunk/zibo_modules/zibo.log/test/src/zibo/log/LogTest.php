<?php

namespace zibo\log;

use zibo\core\Zibo;

use zibo\library\String;

use zibo\log\listener\LogListener;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \Exception;

class LogTest extends BaseTestCase {

    protected function setUp() {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $this->log = new Log();
    }

    public function testConstructedHasTimer() {
        $timer = Reflection::getProperty($this->log, 'timer');

        $this->assertNotNull($timer, 'No timer set to log');
    }

    public function testAddLogListener() {
        $listener = new TestLogListener();

        $this->log->addLogListener($listener);

        $listeners = Reflection::getProperty($this->log, 'listeners');

        $this->assertTrue(in_array($listener, $listeners));
    }

    public function testAddLogItem() {
        $listener = new TestLogListener();
        $this->log->addLogListener($listener);

        $title = 'Test log title';
        $message = 'Test log message';
        $type = LogItem::WARNING;
        $name = 'test';
        $item = new LogItem($title, $message, $type, $name);
        $this->log->addLogItem($item);

        $listener_item = Reflection::getProperty($listener, 'item');

        $this->assertTrue($listener_item == $item, 'Item not added');
        $this->assertTrue($listener_item->getTitle() == $title, 'Title is not the initial title');
        $this->assertTrue($listener_item->getMessage() == $message, 'Message is not the initial message');
        $this->assertTrue($listener_item->getType() == $type, 'Type is not the initial type');
        $this->assertTrue($listener_item->getName() == $name, 'Name is not the initial name');
        $this->assertFalse(String::isEmpty($listener_item->getDate()), 'Date is empty');
        $this->assertFalse(String::isEmpty($listener_item->getMicrotime()), 'Microtime is empty');
        $this->assertFalse(String::isEmpty($listener_item->getIP()), 'IP is empty');
    }

    public function testGetTime() {
        time_nanosleep(0, 300000000);
        $result = $this->log->getTime();
        $this->assertTrue(0.300 <= $result && $result <= 0.302);
    }

}

class TestLogListener implements LogListener {

    private $item;

    public function addLogItem(LogItem $item) {
        $this->item = $item;
    }

    public static function createListenerFromConfig(Zibo $zibo, $name, $configBase) {

    }

}