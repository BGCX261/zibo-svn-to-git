<?php

namespace zibo\library\filesystem;

use zibo\core\Zibo;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class MimeTest extends BaseTestCase {

    public function setUp() {
        $configMock = new ConfigIOMock();
    $configMock->setValues('mime', array( 'txt' => 'text/plain'));
        $browser = $this->getMock('zibo\\library\\filesystem\\browser\\Browser');
        Zibo::getInstance($browser, $configMock);
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    /**
     * @dataProvider providerGetMimeType
     */
    public function testGetMimeType($expected, File $file) {
        $result = Mime::getMimeType($file);
    $this->assertEquals($expected, $result);
    }

    public function providerGetMimeType() {
        return array(
            array('text/plain', new File('test.txt')),
            array(Mime::MIME_UNKNOWN, new File('test.unknown')),
            array(Mime::MIME_UNKNOWN, new File('test')),
    );
    }

}