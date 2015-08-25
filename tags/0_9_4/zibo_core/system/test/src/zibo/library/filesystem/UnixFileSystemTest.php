<?php

namespace zibo\library\filesystem;

use zibo\test\BaseTestCase;

class UnixFileSystemTest extends BaseTestCase {

    /**
     * @dataProvider providerIsRootPath
     */
    public function testIsRootPath($expected, $value) {
        $fs = new UnixFileSystem();
        $result = $fs->isRootPath($value);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsRootPath() {
        return array(
           array(false, 'test/test.txt'),
           array(false, 'test.txt'),
           array(true, '/'),
           array(false, ''),
        );
    }

    /**
     * @dataProvider providerIsAbsolute
     */
    public function testIsAbsolute($expected, $value) {
        $fs = new UnixFileSystem();
        $result = $fs->isAbsolute(new File($value));
        $this->assertEquals($expected, $result, $value);
    }

    public function providerIsAbsolute() {
        return array(
           array(false, 'test/test.txt'),
           array(false, 'test.txt'),
           array(true, '/tmp/test.txt'),
           array(true, 'phar:///tmp/test.txt'),
           array(false, 'phar://test.phar/tmp/test.txt'),
        );
    }

    /**
     * @dataProvider providerGetAbsolutePath
     */
    public function testGetAbsolutePath($expected, $value) {
        $fs = new UnixFileSystem();
        $result = $fs->getAbsolutePath(new File($value));
        $this->assertEquals($expected, $result, $value);
    }

    public function providerGetAbsolutePath() {
        return array(
           array(getcwd() . '/test/test.txt', 'test/.././test/.//./test.txt'),
           array(getcwd() . '/test/test.txt', getcwd() . '/test/.././test/.//./test.txt'),
           array('phar://' . getcwd() . '/modules/test.phar', 'phar://modules/test.phar'),
           array('phar://' . getcwd() . '/modules/test.phar/file.txt', 'modules/test.phar/file.txt'),
        );
    }

    /**
     * @dataProvider providerGetParent
     */
    public function testGetParent($expected, $value) {
        $fs = new UnixFileSystem();
        $file = new File($value);
        $this->assertEquals($expected, $fs->getParent($file));
    }

    public function providerGetParent() {
        return array(
           array(new File('test'), 'test/test.txt'),
           array(new File(getcwd()), 'test.txt'),
           array(new File('/'), '/root'),
           array(new File('/'), '/'),
        );
    }

}