<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\log\filter\LogItemFilter;
use zibo\log\LogItem;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class AbstractFilteredLogListenerTest extends BaseTestCase {

	private $listener;

    public function setUp() {
        $this->listener = new FilteredLogListenerMock();
    }

    /**
     * @dataProvider providerSetInvertThrowsExceptionWhenInvalidValueProvided
     */
    public function testSetInvertThrowsExceptionWhenInvalidValueProvided($value) {
    	try {
    		$this->listener->setInvert($value);
    	} catch (ZiboException $e) {
    		return;
    	}
    	$this->fail();
    }

    public function providerSetInvertThrowsExceptionWhenInvalidValueProvided() {
    	return array(
            array('test'),
            array('-50'),
    	);
    }

    /**
     * @dataProvider providerSetFilterAllFiltersThrowsExceptionWhenInvalidValueProvided
     */
    public function testSetFilterAllFiltersThrowsExceptionWhenInvalidValueProvided($value) {
    	try {
    		$this->listener->setFilterAllFilters($value);
    	} catch (ZiboException $e) {
    		return;
    	}
    	$this->fail();
    }

    public function providerSetFilterAllFiltersThrowsExceptionWhenInvalidValueProvided() {
    	return array(
            array('test'),
            array('-50'),
    	);
    }

    public function testAddLogItemWithoutFilters() {
        $item = $this->getLogItem();
    	$this->listener->addLogItem($item);
    	$this->listener->addLogItem($item);

    	$this->assertEquals(2, $this->listener->getNumLogItems());
    }

    public function testAddLogItemWithAllowFilter() {
        $this->listener->addFilter(new AllowFilter());

        $item = $this->getLogItem();
    	$this->listener->addLogItem($item);

    	$this->assertEquals(1, $this->listener->getNumLogItems());
    }

    public function testAddLogItemWithDenyFilter() {
        $this->listener->addFilter(new DenyFilter());

        $item = $this->getLogItem();
    	$this->listener->addLogItem($item);

    	$this->assertEquals(0, $this->listener->getNumLogItems());
    }

    public function testAddLogItemWithAllFilters() {
        $this->listener->addFilter(new AllowFilter());
        $this->listener->addFilter(new DenyFilter());
        $this->listener->setFilterAllFilters(true);

        $item = $this->getLogItem();
    	$this->listener->addLogItem($item);

        $this->assertEquals(0, $this->listener->getNumLogItems());
    }

    public function testAddLogItemWithInvert() {
    	$this->listener->setInvert(true);

        $item = $this->getLogItem();
    	$this->listener->addLogItem($item);

    	$this->assertEquals(1, $this->listener->getNumLogItems());
    }

    private function getLogItem() {
        $title = 'title';
        $message = 'message';
        $type = LogItem::INFORMATION;
        $name = 'name';
        $item = new LogItem($title, $message, $type, $name);
        $item->setMicrotime('0.001');

    	return $item;
    }

}