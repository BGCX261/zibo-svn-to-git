<?php

namespace zibo\log\filter;

use zibo\log\LogItem;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class NameFilterTest extends BaseTestCase {

    private $filter;

    public function setUp() {
    	$this->filter = new NameFilter();
    }

    public function testAddAllowedName() {
    	$name = 'name';
    	$name2 = 'name2';

    	$this->filter->setAllowedName($name);
    	$this->filter->addAllowedName($name2);

    	$expected = array($name, $name2);

    	$this->assertEquals($expected, Reflection::getProperty($this->filter, 'names'));
    }

    public function testAddAllowedNameThrowExceptionWhenEmptyNameProvided() {
    	try {
            $this->filter->addAllowedName('');
    	} catch (ZiboException $e) {
    		return;
    	}

    	$this->fail();
    }

    public function testSetAllowedName() {
    	$name = 'name';

    	$this->filter->addAllowedName($name);
    	$this->filter->addAllowedName($name);

    	$this->filter->setAllowedName($name);

    	$expected = array($name);

    	$this->assertEquals($expected, Reflection::getProperty($this->filter, 'names'));
    }

    public function testSetAllowedNames() {
    	$name = 'name';
    	$name2 = 'name2';
    	$expected = array($name, $name2);

    	$this->filter->setAllowedNames($expected);

    	$this->assertEquals($expected, Reflection::getProperty($this->filter, 'names'));
    }

    public function testAllowLogItem() {
    	$allow = 'allow';
    	$deny = 'deny';
    	$this->filter->addAllowedName($allow);

    	$item = $this->getLogItem($allow);
    	$this->assertTrue($this->filter->allowLogItem($item));

    	$item = $this->getLogItem($deny);
    	$this->assertFalse($this->filter->allowLogItem($item));

    	$this->filter->addAllowedName($deny);
    	$this->assertTrue($this->filter->allowLogItem($item));
    }

    private function getLogItem($name) {
        $title = 'title';
        $message = 'message';
        $type = LogItem::INFORMATION;
        $name = $name;
        $item = new LogItem($title, $message, $type, $name);
        $item->setMicrotime('0.001');

    	return $item;
    }

}