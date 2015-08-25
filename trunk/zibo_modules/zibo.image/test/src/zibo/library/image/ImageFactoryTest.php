<?php

namespace zibo\library\image;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \Exception;

class ImageFactoryTest extends BaseTestCase {

    public function setUp() {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('The GD extension is not available.');
            return;
        }
        $this->factory = ImageFactory::getInstance();

        $this->mockExtension = 'mock';
        $this->mockResource = imageCreateTrueColor(50, 50);
    }

    public function testGetInstanceHasIO() {
       $io = Reflection::getProperty($this->factory, 'io');
       $this->assertNotNull($io);
    }

    public function testRegister() {
        $this->createEmptyIOMock();

        $this->factory->register($this->mockExtension, $this->mockIO);

        $io = Reflection::getProperty($this->factory, 'io');
        $this->assertArrayHasKey($this->mockExtension, $io);
        $this->assertEquals($this->mockIO, $io[$this->mockExtension]);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testRegisterThrowsExceptionWhenExtensionIsEmpty() {
        $this->createEmptyIOMock();
        $this->factory->register('', $this->mockIO);
    }

    public function testRead() {
        $this->createReadIOMock();

        $this->factory->register($this->mockExtension, $this->mockIO);

        $resource = $this->factory->read($this->mockFile);

        $this->assertEquals($this->mockResource, $resource);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testReadThrowsExceptionWhenExtensionIsNotRegistered() {
        $this->factory->read(new File('image.unsupported'));
    }

    public function testWrite() {
        $this->createWriteIOMock();

        $this->factory->register($this->mockExtension, $this->mockIO);

        $this->factory->write($this->mockFile, $this->mockResource);

        $exists = $this->mockDirectory->exists();
        $this->assertTrue($exists, 'directory of the write file does not exists');
        $this->assertTrue($this->mockIOWrote, 'mock didn\'t write');

        $this->mockDirectory->delete();
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testWriteThrowsExceptionWhenExtensionIsNotRegistered() {
        $this->factory->write(new File('image.unsupported'), $this->mockResource);
    }

    public function writeIOMockWrite() {
        $this->mockIOWrote = true;
    }

    public function createEmptyIOMock() {
        $this->mockIO = $this->getMock('zibo\\library\\image\\io\\ImageIO', array('read', 'write'));
    }

    public function createReadIOMock() {
        $this->mockFile = new File('image.mock');
        $this->mockIO = $this->getMock('zibo\\library\\image\\io\\ImageIO', array('read', 'write'));
        $this->mockIO
            ->expects($this->any())
            ->method('read')
            ->with($this->equalTo($this->mockFile))
            ->will($this->returnValue($this->mockResource));
    }

    public function createWriteIOMock() {
        $this->mockIOWrote = false;
        $this->mockDirectory = new File('application/data/unexistant/');
        $this->mockFile = new File($this->mockDirectory, 'image.mock');
        $this->mockIO = $this->getMock('zibo\\library\\image\\io\\ImageIO', array('read', 'write'), array(), 'WriteIOMock');
        $this->mockIO
            ->expects($this->any())
            ->method('write')
            ->with($this->equalTo($this->mockFile), $this->equalTo($this->mockResource))
            ->will($this->returnCallback(array($this, 'writeIOMockWrite')));
    }

}