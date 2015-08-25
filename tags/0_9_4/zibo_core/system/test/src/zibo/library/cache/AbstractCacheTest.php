<?php

namespace zibo\library\cache;

use zibo\library\cache\io\CacheIO;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class AbstractCacheTest extends BaseTestCase {

    public function testConstruct() {
        $cache = $this->getMockForAbstractClass('zibo\\library\\cache\\AbstractCache');

        $io = Reflection::getProperty($cache, 'io');

        $this->assertNotNull($io);
        $this->assertTrue($io instanceof CacheIO);
    }

    public function testGetDefaultValue() {
        $cache = $this->getMockForAbstractClass('zibo\\library\\cache\\AbstractCache');

        $defaultValue = 'default';

        $value = $cache->get('type', 'id', $defaultValue);

        $this->assertEquals($defaultValue, $value);
    }

}