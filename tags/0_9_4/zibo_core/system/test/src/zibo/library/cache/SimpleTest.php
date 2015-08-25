<?php

namespace zibo\library\cache;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class SimpleCacheTest extends BaseTestCase {

    public function testGet() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object = new CacheObject($type, $id, $value);

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type), $this->equalTo($id))
               ->will($this->returnValue($object));

        $cache = new SimpleCache($IOMock);
        $result = $cache->get($type, $id);

        $this->assertEquals($value, $result);
    }

    public function testGetArray() {
        $type = 'type';
        $id = 'id';
        $value = 'value';

        $objects = array(
            new CacheObject($type, $id . '1', $value . '1'),
            new CacheObject($type, $id . '2', $value . '2'),
            new CacheObject($type, $id . '3', $value . '3'),
        );

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type))
               ->will($this->returnValue($objects));

        $cache = new SimpleCache($IOMock);
        $result = $cache->get($type);

        $values = array(
            'value1',
            'value2',
            'value3',
        );

        $this->assertEquals($values, $result);
    }

    public function testSetValue() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object = new CacheObject($type, $id, $value);

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('writeToCache')
               ->with($this->equalTo($object));

        $cache = new SimpleCache($IOMock);
        $cache->set($type, $id, $value);
    }

}