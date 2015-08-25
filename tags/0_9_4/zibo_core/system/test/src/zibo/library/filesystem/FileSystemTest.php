<?php

namespace zibo\library\filesystem;

use zibo\library\filesystem\exception\FileSystemException;

use zibo\test\BaseTestCase;

use \Exception;

class FileSystemTest extends BaseTestCase {

    protected function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../application');
    }

    protected function tearDown() {
        $this->tearDownApplication();
    }

    public function testGetFileSystemThrowsExceptionOnNoInstance() {
        try {
            $fs = FileSystem::getInstance();
        } catch (FileSystemException $e) {
            return;
        }
        if ($fs == null) {
            $this->fail('getFileSystem should throw an exception when no instance can be returned');
        }
    }

    public function testGetInstance() {
        $fs = FileSystem::getInstance();
        $this->assertNotNull($fs, 'Filesystem creates no instance');
    }

    /**
     * @dataProvider providerExists
     */
    public function testExists($expected, $value) {
        $file = new File($value);
        $result = FileSystem::getInstance()->exists($file);
        $this->assertEquals($expected, $result);
    }

    public function providerExists() {
        return array(
           array(true, '/etc/passwd'),
           array(false, 'unexistant'),
        );
    }

    /**
     * @dataProvider providerIsDirectory
     */
    public function testIsDirectory($expected, $value) {
        $file = new File($value);
        $result = FileSystem::getInstance()->isDirectory($file);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsDirectory() {
        return array(
           array(true, '/etc'),
           array(false, '/etc/passwd'),
           array(true, '.'),
        );
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testIsDirectoryThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $fs->isDirectory($file);
    }

    /**
     * @dataProvider providerIsReadable
     */
    public function testIsReadable($expected, $value) {
        $this->runByRootUserSkipsTest();

        if ($expected === false) {
            chmod($value, 0);
        }
        $file = new File($value);
        $result = FileSystem::getInstance()->isReadable($file);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsReadable() {
        return array(
           array(false, 'application/data/filesystem/unreadable'),
           array(true, '/etc/passwd'),
           array(true, 'application/data/filesystem/writable'),
        );
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testIsReadableThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $fs->isReadable($file);
    }

    /**
     * @dataProvider providerIsWritableWhenFileExists
     */
    public function testIsWritableWhenFileExists($expected, $value) {
        $this->runByRootUserSkipsTest();

        if ($expected == false) {
            chmod($value, 0);
        }
        $file = new File($value);
        $result = FileSystem::getInstance()->isWritable($file);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsWritableWhenFileExists() {
        return array(
           array(true, 'application/data/filesystem/writable'),
           array(false, 'application/data/filesystem/unreadable'),
        );
    }

    /**
     * @dataProvider providerIsWritableWhenFileDoesNotExist
     */
    public function testIsWritableWhenFileDoesNotExist($expected, $value) {
        $this->runByRootUserSkipsTest();

        $file = new File($value);
        $result = FileSystem::getInstance()->isWritable($file);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsWritableWhenFileDoesNotExist() {
        return array(
           array(false, '/root/unexistant'),
           array(true, 'application/data/filesystem/unexistant'),
        );
    }

    public function testGetSize() {
        $fs = FileSystem::getInstance();
        $file = new File('application/data/filesystem/size_12');
        $size = $fs->getSize($file);
        $this->assertEquals('12', $size);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testGetSizeThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $fs->getSize($file);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testGetSizeThrowsExceptionWhenFileIsDirectory() {
        $fs = FileSystem::getInstance();
        $file = new File('.');
        $fs->getSize($file);
    }

    /**
     * @dataProvider providerGetPermissions
     */
    public function testGetPermissions($expected, $value, $message) {
        chmod($value, $expected);

        $file = new File($value);
        $result = FileSystem::getInstance()->getPermissions($file);

        $this->assertEquals($expected, $result, $message);
    }

    public function providerGetPermissions() {
        return array(
           array(0777, 'application/data/filesystem/writable', 'chmod to 777 to make this test succeed'),
           array(0000, 'application/data/filesystem/unreadable', 'chmod to 000 to make this test succeed'),
        );
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testGetPermissionsThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $fs->getPermissions($file);
    }

    public function testSetPermissions() {
        $fs = FileSystem::getInstance();
        $writable = new File('application/data/filesystem/writable');

        $permissions = $writable->getPermissions();
        $testPermissions = 0700;

        $fs->setPermissions($writable, $testPermissions);

        $this->assertEquals($testPermissions, $fs->getPermissions($writable), 'Owner set to the user which runs the test?' . $writable->getPath());

        $fs->setPermissions($writable, $permissions);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testSetPermissionsThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $fs->setPermissions($file, 0644);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testSetPermissionsToProtectedFileThrowsException() {
        $this->runByRootUserSkipsTest();

        $fs = FileSystem::getInstance();
        $file = new File('unreadable');
        $fs->setPermissions($file, 0755);
    }

    public function testReadWithFile() {
        $fs = FileSystem::getInstance();
        $file = new File('application/data/filesystem/size_12');
        $content = $fs->read($file);
        $this->assertEquals('123456789012', $content, 'size_12 modified?');
    }

    public function testReadWithDirectory() {
        $fs = FileSystem::getInstance();

        $basePath = __DIR__ . '/../../../..';
        $dir = new File($basePath);
        $expectations = array(
            $basePath . '/application',
            $basePath . '/data',
            $basePath . '/src',
        );
        $content = $fs->read($dir);
        foreach ($expectations as $expected) {
            $this->assertArrayHasKey($expected, $content, $expected);
        }
    }

    public function providerReadRecursiveWithDirectory() {
        return array(
            array('application/config'),
            array('application/config/variables.ini'),
            array('application/data'),
            array('application/data/filesystem/writable'),
            array('application/data/filesystem/unreadable'),
            array('application/data/filesystem/size_12'),
        );
    }

    /**
     * @dataProvider providerReadRecursiveWithDirectory
     */
    public function testReadRecursiveWithDirectory($expectedPath) {
        $fs = FileSystem::getInstance();

        $dir = new File('application');

        $content = $fs->read($dir, true);
        $this->assertArrayHasKey($expectedPath, $content);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testReadThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $content = $fs->read($file);
    }

    public function testCreate() {
        $fs = FileSystem::getInstance();
        $file = new File('/tmp/test');
        $fs->create($file);
        $this->assertTrue($fs->isDirectory($file));
    }

    public function testCreateRecursive() {
        $fs = FileSystem::getInstance();
        $file = new File('/tmp/test/inner/directory');
        $fs->create($file);
        $this->assertTrue($fs->isDirectory($file));
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testCreateThrowsExceptionWhenFileIsNotWritable() {
        $this->runByRootUserSkipsTest();

        $fs = FileSystem::getInstance();
        $file = new File('/not_writable');
        $fs->create($file);
    }

    public function testDeleteDirectory() {
        $fs = FileSystem::getInstance();
        $dir = new File('/tmp/test');

        if (!$fs->exists($dir)) {
            $dir->create();
        }

        $fs->delete($dir);
        $this->assertFalse($fs->exists($dir));
    }

    public function testDeleteDirectoryWithSubDirectories() {
        $fs = FileSystem::getInstance();
        $dir = new File('/tmp/test');
        $innerDir = new File($dir, 'inner/directory');

        if (!$fs->exists($innerDir)) {
            $fs->create($innerDir);
        }

        $fs->delete($dir);
        $this->assertFalse($fs->exists($dir));
    }

    public function testWrite() {
        $fs = FileSystem::getInstance();
        $file = new File('/tmp/test.txt');

        $content = 'content';
        $fs->write($file, $content);
        $this->assertEquals($content, $fs->read($file));

        $append = 'appended content';
        $fs->write($file, $append, true);
        $this->assertEquals($content . $append, $fs->read($file));

        $fs->delete($file);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testWriteThrowsExceptionWhenFileIsNotWritable() {
        $this->runByRootUserSkipsTest();

        chmod('application/data/filesystem/unreadable', 0000);

        $fs = FileSystem::getInstance();
        $file = new File('application/data/filesystem/unreadable');
        $fs->write($file, 'content');
    }

    public function testDeleteFile() {
        $fs = FileSystem::getInstance();
        $file = new File('/tmp/test.txt');

        if (!$fs->exists($file)) {
            $fs->write($file, 'content');
        }

        $fs->delete($file);
        $this->assertFalse($fs->exists($file));
    }

    public function testGetModificationTime() {
        $fs = FileSystem::getInstance();
        $file = new File('/tmp/test.txt');

        $fs->write($file, 'test');
        $now = time();

        $time = $fs->getModificationTime($file);
        $this->assertEquals($now, $time);

        $fs->delete($file);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testGetModificationTimeThrowsExceptionWhenFileDoesNotExist() {
        $fs = FileSystem::getInstance();
        $file = new File('unexistant');
        $fs->getModificationTime($file);
    }

    public function testLockFile() {
        $path = 'application/data/filesystem/test.txt';

        $fs = FileSystem::getInstance();
        $file = new File($path);
        $lockFile = $file->getLockFile();

        if ($lockFile->exists()) {
            $lockFile->delete();
        }

        $this->assertFalse($lockFile->exists());

        $fs->lock($file);

        $this->assertTrue($lockFile->exists());
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testLockFileThrowsExceptionWhenFileIsLocked() {
        $path = 'application/data/filesystem/test.txt';

        $fs = FileSystem::getInstance();
        $file = new File($path);

        $lockFile = $file->getLockFile();
        $lockFile->write('test');

        $this->assertTrue($lockFile->exists());

        $fs->lock($file, false);
    }

    public function testUnlockFile() {
        $path = 'application/data/filesystem/test.txt';

        $fs = FileSystem::getInstance();
        $file = new File($path);
        $lockFile = $file->getLockFile();

        if (!$lockFile->exists()) {
            $lockFile->write('test');
        }

        $this->assertTrue($lockFile->exists());

        $fs->unlock($file);

        $this->assertFalse($lockFile->exists());
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testUnlockFileThrowsExceptionWhenFileIsNotLocked() {
        $path = 'application/data/filesystem/test.txt';

        $fs = FileSystem::getInstance();
        $file = new File($path);

        $lockFile = $file->getLockFile();
        if ($lockFile->exists()) {
            $lockFile->delete();
        }

        $this->assertFalse($lockFile->exists());

        $fs->unlock($file);
    }

    public function testIsLocked() {
        $path = 'application/data/filesystem/test.txt';

        $fs = FileSystem::getInstance();
        $file = new File($path);

        $lockFile = $file->getLockFile();
        if ($lockFile->exists()) {
            $lockFile->delete();
        }

        $this->assertFalse($fs->isLocked($file));

        $lockFile->write('test');

        $this->assertTrue($fs->isLocked($file));
    }

    public function testCopyFile() {
        $fs = FileSystem::getInstance();

        $source = new File('/tmp/source');
        $fs->write($source, 'contents');
        $destination = new File('/tmp/source.copy');

        $fs->copy($source, $destination);
        $this->assertTrue($fs->exists($destination), 'destination does not exist');
        $this->assertEquals($fs->getSize($source), $fs->getSize($destination), 'source and destination are not the same size');

        $fs->delete($source);
        $fs->delete($destination);
    }

    public function testCopyDirectory() {
        $fs = FileSystem::getInstance();
        $source = new File('/tmp/test');
        $destination = new File('/tmp/test2');
        $innerDir = new File('inner/directory');
        $sourceInnerDir = new File($source, $innerDir);
        $destinationInnerDir = new File($destination, $innerDir);

        if (!$fs->exists($sourceInnerDir)) {
            $fs->create($sourceInnerDir);
        }

        $fs->copy($source, $destination);
        $this->assertTrue($fs->exists($destinationInnerDir), 'destination does not exist');
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testCopyWithUnexistingFileThrowsException() {
        $fs = FileSystem::getInstance();
        $source = new File('unexistant');
        $destination = new File('unexistant.copy');
        $fs->copy($source, $destination);
    }

    public function testMoveFile() {
        $fs = FileSystem::getInstance();

        $source = new File('/tmp/source');
        $fs->write($source, 'contents');
        $sourceSize = $fs->getSize($source);

        $destination = new File('/tmp/source.move');

        $fs->move($source, $destination);
        $this->assertTrue($fs->exists($destination), 'destination does not exist');
        $this->assertFalse($fs->exists($source), 'source does still exist');
        $this->assertEquals($sourceSize, $fs->getSize($destination), 'source and destination are not the same size');

        $fs->delete($destination);
    }

    public function testMoveDirectory() {
        $fs = FileSystem::getInstance();
        $source = new File('/tmp/test');
        $destination = new File('/tmp/test2');
        $innerDir = new File('inner/directory');
        $sourceInnerDir = new File($source, $innerDir);
        $destinationInnerDir = new File($destination, $innerDir);

        if (!$fs->exists($sourceInnerDir)) {
            $fs->create($sourceInnerDir);
        }

        $fs->move($source, $destination);
        $this->assertTrue($fs->exists($destinationInnerDir), 'destination does not exist');
        $this->assertFalse($fs->exists($source), 'source does still exist');

        $fs->delete($destination);
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testMoveWithUnexistingFileThrowsException() {
        $fs = FileSystem::getInstance();
        $source = new File('unexistant');
        $destination = new File('unexistant.move');
        $fs->move($source, $destination);
    }

}