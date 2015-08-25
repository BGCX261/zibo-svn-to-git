<?php

namespace zibo\library\image\thumbnail;

use zibo\library\filesystem\File;
use zibo\library\image\Image;
use zibo\library\image\ImageFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ImageCropThumbnailerTest extends BaseTestCase {

    public function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../..');
        $this->thumbnailer = new CropThumbnailer();
    }

    public function tearDown() {
        $this->tearDownApplication();
        Reflection::setProperty(ImageFactory::getInstance(), 'instance', null);
    }

    public function testGetThumbnail() {
        $image = new Image(new File('application/data/test.jpg'));
        $width = 50;
        $height = 50;

        $thumbnail = $this->thumbnailer->getThumbnail($image, $width, $height);

        $this->assertTrue($thumbnail !== null);
        $this->assertTrue($thumbnail !== $image);
        $this->assertEquals($width, $thumbnail->getWidth());
        $this->assertEquals($height, $thumbnail->getHeight());
    }

}