<?php

namespace zibo\library\archive;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

use \ZipArchive;

class ZipTest extends BaseTestCase {

    private $emptyDir;
    private $file;
    private $files;

    public function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../..');

        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('Zip is unsupported on this system.');
        }
        $this->file = new File('application/data/test.zip');
        $this->prefix = new File('prefix/test');
        $this->compressFiles = array(
            new File('application/data/emptyDirectory'),
            new File(__FILE__),
        );
    }

    public function tearDown() {
        $this->tearDownApplication();
    }

    public function testCompress() {
        $archive = new Zip($this->file);

        $archive->compress($this->compressFiles);

        $this->assertTrue($this->file->exists());
        $this->assertNotEquals(0, $this->file->getSize());

        if ($this->file->exists()) {
            $this->file->delete();
        }
    }

    public function testCompressWithPrefix() {
        $archive = new Zip($this->file);

        $archive->compress($this->compressFiles);
        $archive->compress($this->compressFiles, $this->prefix);

        $this->assertTrue($this->file->exists());
        $this->assertNotEquals(0, $this->file->getSize());

        if ($this->file->exists()) {
            $this->file->delete();
        }
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithEmptyStringThrowsException() {
        $archive = new Zip($this->file);
        $archive->compress('');
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithEmptyArrayThrowsException() {
        $archive = new Zip($this->file);
        $archive->compress(array());
    }

    public function testUncompress() {
        $archive = new Zip($this->file);
        $archive->compress($this->compressFiles);
        $archive->compress($this->compressFiles, $this->prefix);

        $uncompressFiles = array();
        foreach ($this->compressFiles as $file) {
            $uncompressFiles[] = new File($file->getName());
            $uncompressFiles[] = new File($this->prefix, $file->getName());
        }

        $uncompressDirectory = new File('/tmp/zibo/');
        $archive->uncompress($uncompressDirectory);

        try {
            $uncompressedFiles = $uncompressDirectory->read(true);
            foreach ($uncompressFiles as $file) {
                $uncompressedFile = new File($uncompressDirectory, $file);
                $this->assertArrayHasKey($uncompressedFile->getPath(), $uncompressedFiles, $uncompressedFile->getPath());
            }
        } catch (FileSystemException $e) {
            $this->fail('Uncompress directory was not created');
        }

        $uncompressDirectory->delete();
        $this->file->delete();
    }

}