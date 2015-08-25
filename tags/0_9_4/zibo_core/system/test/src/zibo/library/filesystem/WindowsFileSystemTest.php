<?php

namespace zibo\library\filesystem;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class WindowsFileSystemTest extends BaseTestCase {

    /**
     * @dataProvider providerIsRootPath
     */
    public function testIsRootPath($expected, $value) {
        $fs = new WindowsFileSystem();
        $result = $fs->isRootPath($value);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsRootPath() {
        return array(
           array(false, 'test/test.txt'),
           array(false, 'test.txt'),
           array(true, '/'),
           array(true, 'C:/'),
           array(false, '/C'),
           array(true, '/C/'),
           array(false, ''),
        );
    }

    /**
     * @dataProvider providerIsAbsolute
     */
    public function testIsAbsolute($expected, $value) {
        $currentFileSystem = FileSystem::getInstance();
        $windowsFileSystem = new WindowsFileSystem();
        Reflection::setProperty(FileSystem::getInstance(), 'instance', $windowsFileSystem);

        $result = $windowsFileSystem->isAbsolute(new File($value));
        $this->assertEquals($expected, $result, $value);

        Reflection::setProperty(FileSystem::getInstance(), 'instance', $currentFileSystem);
    }

    public function providerIsAbsolute() {
        return array(
           array(false, 'test/test.txt'),
           array(false, 'test.txt'),
           array(true, 'C:/tmp/test.txt'),
           array(true, 'C:\\tmp\\test.txt'),
           array(true, 'D:\\Documents\\test.txt'),
           array(true, '\\\\server\\path\\file.txt'),
           array(true, 'phar://C:/tmp/test.txt'),
           array(false, 'phar://test.phar/tmp/test.txt'),
        );
    }

    /**
     * @dataProvider providerGetAbsolutePath
     */
    public function testGetAbsolutePath($expected, $value) {
        $currentFileSystem = FileSystem::getInstance();
        $windowsFileSystem = new WindowsFileSystem();
        Reflection::setProperty(FileSystem::getInstance(), 'instance', $windowsFileSystem);

        $result = $windowsFileSystem->getAbsolutePath(new File($value));
        $this->assertEquals($expected, $result, $value);

        Reflection::setProperty(FileSystem::getInstance(), 'instance', $currentFileSystem);
    }

    public function providerGetAbsolutePath() {
        return array(
           array(getcwd(), '.'),
           array(getcwd() . '/test/test.txt', 'test/.././test/.//./test.txt'),
           array(getcwd() . '/test/test.txt', getcwd() . '/test/.././test/.//./test.txt'),
           array('phar://' . getcwd() . '/modules/test.phar', 'phar://modules/test.phar'),
           array('phar://D:/modules/test.phar/file.txt', 'D:/modules/test.phar/file.txt'),
        );
    }

    /**
     * @dataProvider providerGetParent
     */
    public function testGetParent($expected, $value) {
        $currentFileSystem = FileSystem::getInstance();
        $windowsFileSystem = new WindowsFileSystem();
        Reflection::setProperty(FileSystem::getInstance(), 'instance', $windowsFileSystem);

        $file = new File($value);
        $this->assertEquals(new File($expected), $windowsFileSystem->getParent($file));

        Reflection::setProperty(FileSystem::getInstance(), 'instance', $currentFileSystem);
    }

    public function providerGetParent() {
        return array(
           array('test', 'test/test.txt'),
           array(getcwd(), 'test.txt'),
           array('/', '/root'),
           array('/', '/'),
           array('C:/folder', 'C:/folder/test.txt'),
           array('C:/', 'C:/test.txt'),
           array('C:/', 'C:/'),
        );
    }

}