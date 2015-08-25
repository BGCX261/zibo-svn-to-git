<?php

namespace zibo\library\uuid;

use zibo\test\BaseTestCase;

class UUIDTest extends BaseTestCase {

    public function testVersion1() {
        $mac = '010203040506';
        $time = time();

        $uuid = UUID::generate(1, $mac);

        $this->assertEquals(1, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
        $this->assertEquals($time, $uuid->getTime());
        $this->assertEquals($mac, $uuid->getNode());
    }

    public function testVersion3() {
        $name = 'zibo.googlecode.com';

        echo $uuid = UUID::generate(3, $name, UUID::NAMESPACE_DNS);

        $this->assertEquals(1, $uuid->getVariant());
        $this->assertEquals(3, $uuid->getVersion());
    }

    public function testVersion4() {
        $uuid = UUID::generate(4);

        $this->assertEquals(1, $uuid->getVariant());
        $this->assertEquals(4, $uuid->getVersion());
    }

    public function testVersion5() {
        $name = 'http://zibo.googlecode.com';

        $uuid = UUID::generate(5, $name, UUID::NAMESPACE_URL);

        $this->assertEquals(1, $uuid->getVariant());
        $this->assertEquals(5, $uuid->getVersion());
    }

}