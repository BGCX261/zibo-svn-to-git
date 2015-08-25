<?php

namespace zibo\library\archive;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

use \Phar as PHPPhar;

class PharTest extends BaseTestCase {

    static $index = 1;

    private $emptyDir;
    private $file;
    private $files;
    private $nonPharFile;
    private $readOnlyFile;

    public function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../..');

        $this->file = new File('application/data/test-' . self::$index++ . '.phar');
        $this->prefix = new File('prefix/test');

        $this->compressFiles = array(
            new File('application/data/emptyDirectory'),
            new File(__DIR__ . '/../../../../../build.xml'),
        );

        $this->nonPharFile = new File('application/data/test.gif');
    }

    public function tearDown() {
        $this->tearDownApplication();
        clearstatcache();
    }

    public function testCompress() {
        $archive = new Phar($this->file);

        $archive->compress($this->compressFiles);

        $this->assertTrue($this->file->exists());
        $this->assertNotEquals(0, $this->file->getSize());

        $this->file->delete();
        clearstatcache();
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithNonPharPathThrowsException() {
        $archive = new Phar($this->nonPharFile);
        $archive->compress($this->compressFiles);
    }

    public function testCompressWithNonExistingPharPathCreatesPath() {
        $nonExistingPath = new File('application/data/non_existing_dir/test.phar');
        $archive = new Phar($nonExistingPath);
        $archive->compress($this->compressFiles);

        if (!$nonExistingPath->exists()) {
            $this->fail();
        }

        $nonExistingPath->getParent()->delete();
    }

    public function testCompressWithPrefix() {
        $archive = new Phar($this->file);

        $archive->compress($this->compressFiles);
        $archive->compress($this->compressFiles, $this->prefix);

        $this->assertTrue($this->file->exists());
        $this->assertNotEquals(0, $this->file->getSize());
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithEmptyStringThrowsException() {
        $archive = new Phar($this->file);
        $archive->compress('');
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithNonFileSourceThrowsArchiveException() {
        $archive = new Phar($this->file);
        $archive->compress('something_that_is_not_an_instance_of_file');
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithNonExistingFileSourceThrowsArchiveException() {
        $archive = new Phar($this->file);
        $archive->compress(new File('non-existing'));
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testCompressWithEmptyArrayThrowsException() {
        $archive = new Phar($this->file);
        $archive->compress(array());
    }

    public function testUncompress() {
        $archive = new Phar($this->file);

        $archive->compress($this->compressFiles);
        $archive->compress($this->compressFiles, $this->prefix);

        $uncompressDirectory = new File('application/data/uncompress');
        if ($uncompressDirectory->exists()) {
            $uncompressDirectory->delete();
        }

        $uncompressFiles = array();
        foreach ($this->compressFiles as $file) {
            $uncompressFiles[] = new File($file->getName());
            $uncompressFiles[] = new File($this->prefix, $file->getName());
        }

        try {
            $archive->uncompress($uncompressDirectory);
        } catch (FileSystemException $e) {
            $this->fail('Uncompress directory was not created');
        }

        $contentBuildXml = $this->compressFiles[1]->read();

        $uncompressedFiles = $uncompressDirectory->read(true);
        foreach ($uncompressFiles as $file) {
            $uncompressedFile = new File($uncompressDirectory, $file);
            $this->assertArrayHasKey($uncompressedFile->getPath(), $uncompressedFiles, $uncompressedFile->getPath());

            if ($file->getName() == 'build.xml' && $uncompressedFile->read() != $contentBuildXml) {
                $this->fail('Content of the uncompressed build.xml is not the same as the original file');
            }
        }

        $uncompressDirectory->delete();
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testUncompressWithNonPharPathThrowsException() {
        $phar = new Phar($this->nonPharFile);

        $uncompressDirectory = new File('/tmp/zibo/');
        if ($uncompressDirectory->exists()) {
            $uncompressDirectory->delete();
        }

        $phar->uncompress($uncompressDirectory);
    }

}