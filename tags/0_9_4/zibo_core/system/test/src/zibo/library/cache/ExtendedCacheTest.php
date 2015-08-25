<?php

namespace zibo\library\cache;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ExtendedCacheTest extends BaseTestCase {

    public function testGet() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object = new CacheObject($type, $id, $value);

        $time = time();
        $accessed = 0;

        $this->assertEquals($time, $object->getCreationDate());
        $this->assertEquals($time, $object->getLastAccessDate());
        $this->assertEquals($accessed, $object->getTimesAccessed());

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type), $this->equalTo($id))
               ->will($this->returnValue($object));

        sleep(1);
        $updatedTime = $time + 1;
        $accessed++;

        $cache = new ExtendedCache($IOMock);
        $result = $cache->get($type, $id);

        $this->assertEquals($value, $result);
        $this->assertEquals($time, $object->getCreationDate());
        $this->assertEquals($updatedTime, $object->getLastAccessDate());
        $this->assertEquals($accessed, $object->getTimesAccessed());
    }

    public function testGetArray() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object1 = new CacheObject($type, $id . '1', $value . '1');
        $object2 = new CacheObject($type, $id . '2', $value . '2');
        $object3 = new CacheObject($type, $id . '3', $value . '3');
        $objects = array(
            $object1,
            $object2,
            $object3,
        );

        $time = time();
        $accessed = 0;

        $this->assertEquals($time, $object1->getCreationDate());
        $this->assertEquals($time, $object1->getLastAccessDate());
        $this->assertEquals($accessed, $object1->getTimesAccessed());
        $this->assertEquals($time, $object2->getCreationDate());
        $this->assertEquals($time, $object2->getLastAccessDate());
        $this->assertEquals($accessed, $object2->getTimesAccessed());
        $this->assertEquals($time, $object3->getCreationDate());
        $this->assertEquals($time, $object3->getLastAccessDate());
        $this->assertEquals($accessed, $object3->getTimesAccessed());

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type))
               ->will($this->returnValue($objects));

        sleep(1);
        $updatedTime = $time + 1;
        $accessed++;

        $cache = new ExtendedCache($IOMock);
        $result = $cache->get($type);

        $values = array(
            'value1',
            'value2',
            'value3',
        );

        $this->assertEquals($values, $result);
        $this->assertEquals($time, $object2->getCreationDate());
        $this->assertTrue($object2->getLastAccessDate() > $updatedTime - 1);
        $this->assertTrue($object2->getLastAccessDate() < $updatedTime + 1);
        $this->assertEquals($accessed, $object2->getTimesAccessed());

    }

    public function testGetCleansUpWhenCleanUpTimeReached() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object = new CacheObject($type, $id, $value);
        $object->access();
        $object->access();

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type), $this->equalTo($id))
               ->will($this->returnValue($object));
        $IOMock->expects($this->once())
               ->method('clearCache')
               ->with($this->equalTo($type), $this->equalTo($id));

        $cache = new ExtendedCache($IOMock);
        $cache->setCleanUpTimes(2);

        $cache->get($type, $id);
    }

    public function testGetCleansUpWhenCleanUpAgeReached() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object = new CacheObject($type, $id, $value);

        sleep(3);
        $object->access();

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type), $this->equalTo($id))
               ->will($this->returnValue($object));
        $IOMock->expects($this->once())
               ->method('clearCache')
               ->with($this->equalTo($type), $this->equalTo($id));

        $cache = new ExtendedCache($IOMock);
        $cache->setCleanUpAge(2);

        $cache->get($type, $id);
    }

    public function testSetNewValue() {
        $type = 'type';
        $id = 'id';
        $value = 'value';
        $object = new CacheObject($type, $id, $value);

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('writeToCache')
               ->with($this->equalTo($object));

        $cache = new ExtendedCache($IOMock);
        $cache->set($type, $id, $value);
    }

    public function testSetExistingValue() {
        $type = 'type';
        $id = 'id';
        $oldValue = 'old';
        $value = 'value';
        $object = new CacheObject($type, $id, $oldValue);

        $IOMock = $this->getMock('zibo\\library\\cache\\io\\CacheIO', array('readFromCache', 'clearCache', 'writeToCache'));
        $IOMock->expects($this->once())
               ->method('readFromCache')
               ->with($this->equalTo($type), $this->equalTo($id))
               ->will($this->returnValue($object));

        $cache = new ExtendedCache($IOMock);
        $cache->set($type, $id, $value);

        $this->assertEquals($value, $object->getData());
        $this->assertEquals(0, $object->getTimesAccessed());
    }

}