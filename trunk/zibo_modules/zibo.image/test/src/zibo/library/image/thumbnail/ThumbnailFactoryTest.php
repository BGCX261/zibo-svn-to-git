<?php

namespace zibo\library\image\thumbnail;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ThumbnailException;
use zibo\library\image\Image;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \Exception;

class ThumbnailFactoryTest extends BaseTestCase {

    public function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../..');
        $this->factory = ThumbnailFactory::getInstance();
        $this->mockName = 'mock';
        $this->mockImage = new Image(new File('application/data/test.jpg'));
        $this->mockThumbnailSize = 50;
    }

    public function tearDown() {
        $this->tearDownApplication();
    }

    public function testGetInstanceHasThumbnailers() {
       $thumbnailers = Reflection::getProperty($this->factory, 'thumbnailers');
       $this->assertNotNull($thumbnailers);
    }

    public function testRegister() {
        $this->createEmptyThumbnailerMock();

        $this->factory->register($this->mockName, $this->mockThumbnailer);

        $thumbnailers = Reflection::getProperty($this->factory, 'thumbnailers');
        $this->assertArrayHasKey($this->mockName, $thumbnailers);
        $this->assertEquals($this->mockThumbnailer, $thumbnailers[$this->mockName]);
    }

    public function testRegisterThrowsNameWhenExtensionIsEmpty() {
        $this->createEmptyThumbnailerMock();
        try {
            $this->factory->register('', $this->mockThumbnailer);
        } catch (ThumbnailException $e) {
            return;
        }
        $this->fail();
    }

    public function testGetThumbnail() {
        $this->createThumbnailerMock();

        $this->factory->register($this->mockName, $this->mockThumbnailer);

        $this->factory->getThumbnail($this->mockName, $this->mockImage, $this->mockThumbnailSize, $this->mockThumbnailSize);

        $this->assertTrue($this->mockThumbnailerCreated, 'mock didn\'t create');
    }

    public function testGetThumbnailThrowsExceptionWhenNameIsNotRegistered() {
        try {
            $this->factory->getThumbnail('unexistant', $this->mockImage, $this->mockThumbnailSize, $this->mockThumbnailSize);
        } catch (ThumbnailException $e) {
            return;
        }
        $this->fail();
    }

    public function mockThumbnailerGetThumbnail() {
        $this->mockThumbnailerCreated = true;
    }

    public function createThumbnailerMock() {
        $this->mockThumbnailerCreated = false;

        $this->mockThumbnailer = $this->getMock('zibo\\library\\image\\thumbnail\\Thumbnailer', array('getThumbnail'));

        $this->mockThumbnailer
            ->expects($this->any())
            ->method('getThumbnail')
            ->with($this->equalTo($this->mockImage), $this->equalTo($this->mockThumbnailSize), $this->equalTo($this->mockThumbnailSize))
            ->will($this->returnCallback(array($this, 'mockThumbnailerGetThumbnail')));
    }

    public function createEmptyThumbnailerMock() {
        $this->mockThumbnailer = $this->getMock('zibo\\library\\image\\thumbnail\\Thumbnailer', array('getThumbnail'));
    }

}