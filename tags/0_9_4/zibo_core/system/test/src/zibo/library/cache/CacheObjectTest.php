<?php

namespace zibo\library\cache;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class CacheObjectTest extends BaseTestCase {

    public function testConstruct() {
        $time = time();
        $timesAccessed = 0;
        $type = 'type';
        $id = 'id';
        $data = 'value';
        $cache = new CacheObject($type, $id, $data);
        $this->assertEquals($type, $cache->getType());
        $this->assertEquals($id, $cache->getId());
        $this->assertEquals($data, $cache->getData());
        $this->assertEquals($timesAccessed, $cache->getTimesAccessed());
        $this->assertEquals($time, $cache->getCreationDate());
        $this->assertEquals($time, $cache->getLastAccessDate());
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidTypeOrIdProvided
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidTypeOrIdProvided($type, $id) {
        new CacheObject($type, $id, null);
    }

    public function providerConstructThrowsExceptionWhenInvalidTypeOrIdProvided() {
        return array(
            array('', 'id'),
            array($this, 'id'),
            array('type', ''),
            array('type', $this),
        );
    }

    public function testAccess() {
        $time = time();

        $cache = new CacheObject('type', 'id', '');

        sleep(1);
        $cache->access();
        $accessTime = $time + 1;

        $this->assertEquals(1, $cache->getTimesAccessed());
        $this->assertEquals($time, $cache->getCreationDate());
        $this->assertEquals($accessTime, $cache->getLastAccessDate());
    }

}