<?php

namespace zibo\library\image\thumbnail;

use zibo\library\filesystem\File;
use zibo\library\image\Image;
use zibo\library\image\ImageFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ImageResizeThumbnailerTest extends BaseTestCase {

    public function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../..');
        $this->thumbnailer = new ResizeThumbnailer();
    }

    public function tearDown() {
        $this->tearDownApplication();
        Reflection::setProperty(ImageFactory::getInstance(), 'instance', null);
    }

    public function testGetThumbnail() {
        $image = new Image(new File('application/data/test.jpg'));
        $thumbnailWidth = 50;
        $thumbnailHeight = 50;

        $expectedWidth = 50;
        $expectedHeight = 39;

        $thumbnail = $this->thumbnailer->getThumbnail($image, $thumbnailWidth, $thumbnailHeight);

        $this->assertTrue($thumbnail !== null);
        $this->assertTrue($thumbnail !== $image);
        $this->assertEquals($expectedWidth, $thumbnail->getWidth());
        $this->assertEquals($expectedHeight, $thumbnail->getHeight());
    }

}