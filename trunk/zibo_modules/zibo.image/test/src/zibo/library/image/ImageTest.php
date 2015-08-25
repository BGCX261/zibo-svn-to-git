<?php

namespace zibo\library\image;

use zibo\library\filesystem\File;
use zibo\library\image\io\JpgImageIO;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \Exception;

class ImageTest extends BaseTestCase {

    protected function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../..');
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('The GD extension is not available.');
            return;
        }
        $imageFactory = ImageFactory::getInstance();
        $imageFactory->register('jpg', new JpgImageIO());

        $this->testImage = new File('application/data/test.jpg');
        $this->testImageWidth = 300;
        $this->testImageHeight = 232;
    }

    public function tearDown() {
        $this->tearDownApplication();
    }

    public function testConstruct() {
        $image = new Image();

        $resource = Reflection::getProperty($image, 'resource');
        $this->assertNotNull($resource);

        $width = Reflection::getProperty($image, 'width');
        $this->assertNotNull($width);

        $height = Reflection::getProperty($image, 'height');
        $this->assertNotNull($height);
    }

    public function testConstructWithWidthAndHeight() {
        $width = 500;
        $height = 400;
        $image = new Image(null, $width, $height);

        $imageWidth = Reflection::getProperty($image, 'width');
        $this->assertEquals($width, $imageWidth);

        $imageHeight = Reflection::getProperty($image, 'height');
        $this->assertEquals($height, $imageHeight);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testConstructWithWidthAndHeightThrowsExceptionWhenWidthIsInvalid() {
        new Image(null, -1, 100);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testConstructWithWidthAndHeightThrowsExceptionWhenHeightIsInvalid() {
        new Image(null, 100, -1);
    }

    public function testConstructWithImage() {
        $width = 500;
        $height = 400;
        $testImage = new Image(null, $width, $height);

        $image = new Image($testImage);

        $resource = Reflection::getProperty($image, 'resource');
        $testResource = Reflection::getProperty($testImage, 'resource');
        $this->assertNotNull($testResource);
        $this->assertNotEquals($resource, $testResource);

        $width = Reflection::getProperty($image, 'width');
        $testWidth = Reflection::getProperty($testImage, 'width');
        $this->assertEquals($width, $testWidth);

        $height = Reflection::getProperty($image, 'height');
        $testHeight = Reflection::getProperty($testImage, 'height');
        $this->assertEquals($height, $testHeight);
    }

    public function testConstructWithFile() {
        $image = new Image($this->testImage);

        $resource = Reflection::getProperty($image, 'resource');
        $this->assertNotNull($resource);
    }

    public function testGetWidth() {
        $image = new Image($this->testImage);
        $width = $image->getWidth();
        $this->assertEquals($this->testImageWidth, $width);
    }

    public function testGetHeight() {
        $image = new Image($this->testImage);
        $height = $image->getHeight();
        $this->assertEquals($this->testImageHeight, $height);
    }

    public function testCrop() {
        $image = new Image($this->testImage);

        $cropX = 10;
        $cropY = 10;
        $cropWidth = ($this->testImageWidth / 2) - $cropX;
        $cropHeight = ($this->testImageHeight / 2) - $cropY;

        $croppedImage = $image->crop($cropX, $cropY, $cropWidth, $cropHeight);

        $this->assertNotNull($croppedImage, 'cropped image is null');

        $croppedImageWidth = $croppedImage->getWidth();
        $this->assertEquals($cropWidth, $croppedImageWidth, 'width is not the requested width');
        $croppedImageHeight = $croppedImage->getHeight();
        $this->assertEquals($cropHeight, $croppedImageHeight, 'height is not the requested height');
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testCropThrowsExceptionWhenXIsBelowZero() {
        $image = new Image($this->testImage);
        $image->crop(-1, 10, 10, 10);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testCropThrowsExceptionWhenXExceedsImage() {
        $image = new Image($this->testImage);
        $image->crop($this->testImageWidth + 10, 10, 10, 10);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testCropThrowsExceptionWhenYIsBelowZero() {
        $image = new Image($this->testImage);
        $image->crop(10, -1, 10, 10);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testCropThrowsExceptionWhenYExceedsImage() {
        $image = new Image($this->testImage);
        $image->crop(10, $this->testImageHeight + 10, 10, 10);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testCropThrowsExceptionWhenXAndWidthExceedsImage() {
        $image = new Image($this->testImage);
        $image->crop(10, 10, $this->testImageWidth + 10, 10);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testCropThrowsExceptionWhenYAndHeightExceedsImage() {
        $image = new Image($this->testImage);
        $image->crop(10, 10, 10, $this->testImageHeight + 10);
    }

    public function testResize() {
        $image = new Image($this->testImage);

        $resizeWidth = $this->testImageWidth / 2;
        $resizeHeight = $this->testImageHeight / 2;

        $resizedImage = $image->resize($resizeWidth, $resizeHeight);

        $this->assertNotNull($resizedImage, 'resizedimage is null');

        $resuzedImageWidth = $resizedImage->getWidth();
        $this->assertEquals($resizeWidth, $resuzedImageWidth, 'width is not the requested width');
        $resizedImageHeight = $resizedImage->getHeight();
        $this->assertEquals($resizeHeight, $resizedImageHeight, 'height is not the requested height');
    }

}