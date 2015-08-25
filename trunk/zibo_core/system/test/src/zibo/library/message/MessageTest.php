<?php

namespace zibo\library\message;

use zibo\test\BaseTestCase;

class MessageTest extends BaseTestCase {

    public function testConstruct() {
        $message = 'message';
        $type = 'type';

        $msg = new Message($message, $type);

        $this->assertEquals($message, $msg->getMessage());
        $this->assertEquals($type, $msg->getType());

        $msg = new Message($message);

        $this->assertEquals($message, $msg->getMessage());
        $this->assertNull($msg->getType());
    }

    /**
     * @dataProvider providerConstructWithInvalidValuesThrowsException
     * @expectedException zibo\ZiboException
     */
    public function testConstructWithInvalidValuesThrowsException($message, $type) {
        new Message($message, $type);
    }

    public function providerConstructWithInvalidValuesThrowsException() {
        return array(
            array('', 'value'),
            array(null, 'value'),
            array(array(), 'value'),
            array($this, 'value'),
            array('name', ''),
            array('name', array()),
            array('name', $this),
        );
    }

}