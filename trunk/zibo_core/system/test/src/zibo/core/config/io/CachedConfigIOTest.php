<?php

namespace zibo\core\config\io;

use zibo\library\cache\Cache;
use zibo\library\cache\ExtendedCache;

use zibo\test\mock\ConfigIOMock;
use zibo\test\mock\CacheIOMock;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class CachedConfigIOTest extends BaseTestCase {

    const ENVIRONMENT_NAME = 'foo';

    /**
     * @var zibo\library\cache\Cache
     */
    protected $cache;

    /**
     * @var zibo\library\config\io\ConfigIO
     */
    protected $innerIO;

    /**
     * @var zibo\library\config\io\ConfigIO
     */
    protected $io;

    protected function setUp() {
        $environment = $this->getMock('zibo\\core\\environment\\Environment', array('getName', 'generateBaseUrl', 'getRequestedPath', 'getQueryArguments', 'getBodyArguments'));
        // environment needs to return a name, otherwise an error occurs in the cache library
        $environment->expects($this->any())->method('getName')->will($this->returnValue(CachedConfigIOTest::ENVIRONMENT_NAME));

        $this->innerIO = new ConfigIOMock();
        $this->innerIO->setValues('file', array('key' => 'value'));
        $this->innerIO->setValues('only_in_config', array('foo' => 'bar'));

        $this->cache = new ExtendedCache(new CacheIOMock());
        $this->cache->set(CachedConfigIOTest::ENVIRONMENT_NAME, 'file', array('another_key' => 'another_value'));
        $this->cache->set(CachedConfigIOTest::ENVIRONMENT_NAME, 'only_in_cache', array('test' => '123'));

        $this->io = new CachedConfigIO($this->innerIO, $environment, $this->cache);
    }

    protected function tearDown() {
        unset($this->io);
        unset($this->cache);
        unset($this->innerIO);
    }

//     public function testConstructDoesNotEnableCaching() {
//         $this->assertNull(Reflection::getProperty($this->io, 'isCacheEnabled'));
//     }

    public function providerReadWithoutCache() {
        return array(
            array(
                'file',
                array('key' => 'value'),
                array('another_key' => 'another_value'),
            ),
            array(
                'only_in_config',
                array('foo' => 'bar'),
                null,
            ),
            array(
                'only_in_cache',
                array(),
                array('test' => '123'),
             ),
        );
    }

    /**
     * @dataProvider providerReadWithoutCache
     */
    public function testReadWithoutCache($section, $expectedInConfig, $expectedInCache) {
        $this->io->setIsCacheEnabled(false);

        $actual = $this->io->read($section);
        $this->assertEquals($expectedInConfig, $actual);

        $actual = $this->cache->get(self::ENVIRONMENT_NAME, $section);
        $this->assertEquals($expectedInCache, $actual);
    }

    public function providerReadWithCache() {
        return array(
          array(
              'file',
              array('another_key' => 'another_value'),
              array('another_key' => 'another_value'),
          ),
          array(
              'only_in_config',
              array('foo' => 'bar'),
              array('foo' => 'bar'),
          ),
          array(
              'only_in_cache',
              array('test' => '123'),
              array('test' => '123'),
          ),
        );
    }

    /**
     * @dataProvider providerReadWithCache
     */
    public function testReadWithCache($section, $expectedInConfig, $expectedInCache) {
        $this->io->setIsCacheEnabled(true);

        $actual = $this->io->read($section);
        $this->assertEquals($expectedInConfig, $actual);

        $actual = $this->cache->get(CachedConfigIOTest::ENVIRONMENT_NAME, $section);
        $this->assertEquals($actual, $expectedInCache);
    }

    public function testReadAllWithoutCache() {
        $this->io->setIsCacheEnabled(false);

        $expected = array(
            'file' => array('key' => 'value'),
            'only_in_config' => array('foo' => 'bar'),
        );
        $actual = $this->io->readAll();
        $this->assertEquals($expected, $actual);
    }

    // TODO: CachedConfigIO currently does not return sections
    // that were cached but do not exist in config anymore, this
    // probably needs to be fixed somehow
    public function testReadAllWithCache() {
        $this->io->setIsCacheEnabled(true);

        $this->cache->get(self::ENVIRONMENT_NAME, 'file');

        $expected = array(
            'file' => array('another_key' => 'another_value'),
            'only_in_cache' => array('test' => '123'),
            'only_in_config' => array('foo' => 'bar'),
        );
        $actual = $this->io->readAll();
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllSectionsWithoutCache() {
        $this->io->setIsCacheEnabled(false);

        $expected = array('file', 'only_in_config');
        $this->assertEquals($expected, $this->io->getAllSections());
    }

    public function testGetAllSectionsWithCache() {
        $this->io->setIsCacheEnabled(true);

        $expected = array('file', 'only_in_cache', 'only_in_config');
        $this->assertEquals($expected, $this->io->getAllSections());
    }
}
