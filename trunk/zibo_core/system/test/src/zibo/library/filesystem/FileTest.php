<?php

namespace zibo\library\filesystem;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class FileTest extends BaseTestCase {

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($expected, $path, $child) {
        $file = new File($path, $child);
        $filePath = Reflection::getProperty($file, 'path');
        $this->assertEquals($expected, $filePath);
    }

    public function providerConstruct() {
        return array(
           array('test/test.txt', 'test/test.txt', null),
           array('test/test.txt', new File('test/test.txt'), null),
           array('test', 'test/', null),
           array('test.phar', 'test.phar', null),
           array('phar://test.phar/file.txt', 'test.phar/file.txt', null),
           array('/', '/', null),
           array('test/test.txt', 'test/', 'test.txt'),
           array('test/tester/test.txt', 'test/tester', 'test.txt'),
           array('test/tester/test.txt', 'test/tester', new File('test.txt')),
           array('test/tester/test.txt', new File('test/tester'), new File('test.txt')),
           array('phar://test/test.phar/test/test.txt', 'test/test.phar', 'test/test.txt'),
           array('phar://test/test.phar/test/test.txt', 'phar://test/test.phar', 'test/test.txt'),
           array('phar://folder/test.phar/file.txt', 'folder', 'phar://test.phar/file.txt'),
        );
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testConstructWithEmptyPathThrowsException() {
        $file = new File('');
    }

    /**
     * @expectedException zibo\library\filesystem\exception\FileSystemException
     */
    public function testConstructWithAbsoluteChildThrowsException() {
        $file = new File('test/test', '/tmp/test.txt');
    }

    public function testGetPath() {
        $path = 'test/test.txt';
        $file = new File($path);
        $this->assertEquals($path, $file->getPath(), 'Path is not the set path');
    }

    /**
     * @dataProvider providerGetName
     */
    public function testGetName($expected, $value) {
        $file = new File($value);
        $this->assertEquals($expected, $file->getName());
    }

    public function providerGetName() {
        return array(
           array('test.txt', 'test.txt'),
           array('test.txt', 'test/test.txt'),
        );
    }

    /**
     * @dataProvider providerGetExtension
     */
    public function testGetExtension($expected, $value) {
        $file = new File($value);
        $this->assertEquals($expected, $file->getExtension());
    }

    public function providerGetExtension() {
        return array(
           array('txt', 'test.txt'),
           array('', 'test'),
           array('htaccess', '.htaccess'),
           array('zip', '.test.ZIP'),
           array('xml', '/test/config/modules.xml'),
           array('', './test/config'),
           array('', './test.folder/config'),
        );
    }

    /**
     * @dataProvider providerHasExtension
     */
    public function testHasExtension($expected, $value) {
        $file = new File('test.txt');
        $result = $file->hasExtension($value);
        $this->assertEquals($expected, $result, var_export($value, true));
    }

    public function providerHasExtension() {
        return array(
           array(true, 'txt'),
           array(false, 'test'),
           array(false, array('png', 'jpg')),
           array(true, array('png', 'jpg', 'txt')),
        );
    }

    /**
     * @dataProvider providerIsInPhar
     */
    public function testIsInPhar($expected, $value) {
        $file = new File($value);
        $result = $file->isInPhar($file);
        $this->assertEquals($expected, $result);
    }

    public function providerIsInPhar() {
        return array(
           array(false, 'file.txt'),
           array(false, 'file.phar'),
           array(new File('test.phar'), 'test.phar/file.txt'),
        );
    }

    /**
     * @dataProvider providerGetCopyFile
     */
    public function testGetCopyFile($expected, $value) {
        $file = new File($value);
        $copyFile = $file->getCopyFile($file);
        $this->assertEquals($expected, $copyFile->getPath(), $value);
    }

    public function providerGetCopyFile() {
        return array(
           array('/etc/passwd-1', '/etc/passwd'),
           array('test/unexistant', 'test/unexistant'),
        );
    }

    /**
     * @dataProvider providerGetLockFile
     */
    public function testGetLockFile($expected, $value) {
        $file = new File($value);
        $lockFile = $file->getLockFile($file);
        $this->assertEquals($expected, $lockFile->getPath(), $value);
    }

    public function providerGetLockFile() {
        return array(
           array('directory/file.txt.lock', 'directory/file.txt'),
        );
    }

}