<?php

namespace zibo\library\cache\io;

use zibo\library\cache\CacheObject;
use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

class FileCacheIOTest extends BaseTestCase {

    const PATH_WRITE = 'application/data/cache';
    const PATH_READ = 'application/data';

    private $cache;
    private $path;

    public function testWriteToCache() {
        $this->setUpWriteCache();

        $type = 'type';
        $id = 'id';
        $value = 'Test value';
        $object = new CacheObject($type, $id, $value);
        $this->cache->writeToCache($object);

        $cacheFile = new File($this->path, $type . File::DIRECTORY_SEPARATOR . $id);
        $this->assertEquals(serialize($object), $cacheFile->read());

        $this->tearDownCache();
    }

    public function testReadFromCache() {
        $this->setUpReadCache();

        $type = 'cache';
        $id = 'testRead';
        $expected = 'Test value';
        $object = new CacheObject($type, $id, $expected);

        $cachePath = new File($this->path, $type);
        $cachePath->create();

        $cacheFile = new File($this->path, $type . File::DIRECTORY_SEPARATOR . $id);
        $cacheFile->write(serialize($object));

        $result = $this->cache->readFromCache($type, $id);

        $this->assertEquals($object, $result);
        $this->assertEquals($expected, $result->getData());

        $this->tearDownCache();
    }

    public function testClearCache() {
        $this->setUpWriteCache();

        $type = 'type';
        $values = array(
            'id1' => 'value1',
            'id2' => 'value2',
        );

        foreach ($values as $id => $value) {
            $object = new CacheObject($type, $id, $value);
            $this->cache->writeToCache($object);
        }

        $cachePath = new File($this->path, $type);
        $cacheFile1 = new File($cachePath, 'id1');
        $cacheFile2 = new File($cachePath, 'id2');

        $this->assertEquals(true, $cacheFile1->exists());
        $this->assertEquals(true, $cacheFile2->exists());

        $this->cache->clearCache($type, 'id1');

        $this->assertEquals(false, $cacheFile1->exists());
        $this->assertEquals(true, $cacheFile2->exists());

        $this->cache->clearCache($type);

        $this->assertEquals(false, $cachePath->exists());

        $this->tearDownCache();
    }

    private function setUpWriteCache() {
        $this->path = new File(self::PATH_WRITE);
        $this->path->create();
        $this->cache = new FileCacheIO($this->path);
    }

    private function setUpReadCache() {
        $this->path = new File(self::PATH_READ);
        $this->path->create();
        $this->cache = new FileCacheIO($this->path);
    }

    private function tearDownCache() {
        if ($this->path && $this->path->exists()) {
            $this->path->delete();
        }
    }

}