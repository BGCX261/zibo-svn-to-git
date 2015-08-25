<?php

namespace zibo\library\image\io;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;

use zibo\test\BaseTestCase;

class PngImageIOTest extends BaseTestCase {

    protected function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../..');
        $this->io = new PngImageIO();
    }

    public function tearDown() {
        $this->tearDownApplication();
    }

    public function testRead() {
        $resource = $this->io->read(new File('application/data/test.png'));
        $this->assertNotNull($resource);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testReadThrowsExceptionWhenFileHasWrongExtension() {
        $this->io->read(new File('application/data/test.gif'));
    }

    public function testReadThrowsExceptionWhenFileIsNotReadable() {
        $this->runByRootUserSkipsTest();

        try {
            $this->io->read(new File('application/data/unreadable.png'));
        } catch (ImageException $e) {
           return;
        } catch (FileSystemException $e) {
           return;
        }
        $this->fail();
    }

    public function testWrite() {
        $file = new File('application/data/new.png');
        $this->io->write($file, imageCreateTrueColor(100, 100));

        $exists = $file->exists();
        $this->assertTrue($exists, 'image is not written');
        $size = $file->getSize();
        $this->assertFalse($size == 0, 'image is empty');

        $file->delete();
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testWriteThrowsExceptionWhenResourceIsNull() {
        $this->io->write(new File('application/data/new.png'), null);
    }

    /**
     * @expectedException zibo\library\image\exception\ImageException
     */
    public function testWriteThrowsExceptionWhenFileHasWrongExtension() {
        $this->io->write(new File('application/data/test.gif'), imageCreateTrueColor(100, 100));
    }

    public function testWriteThrowsExceptionWhenFileIsNotWritable() {
        $this->runByRootUserSkipsTest();

        try {
            $this->io->write(new File('/etc/test.png'), imageCreateTrueColor(100, 100));
        } catch (ImageException $e) {
           return;
        } catch (FileSystemException $e) {
           return;
        }
        $this->fail();
    }

}