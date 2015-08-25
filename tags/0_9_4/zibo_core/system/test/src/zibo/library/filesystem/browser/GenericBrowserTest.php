<?php

namespace zibo\library\filesystem\browser;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

use \Exception;

class GenericBrowserTest extends BaseTestCase {

    private $rootPath;

    private $browser;

    protected function setUp() {
        $ziboPath = realpath(dirname(__FILE__) . '/../../../../../../../');
        $this->rootPath = new File($ziboPath);
        $this->browser = new GenericBrowser($this->rootPath);
    }

    public function providerGetFileThrowsExceptionWhenInvalidArgumentProvided() {
        return array(
            array(''),
            array(null),
            array($this),
            array(array()),
        );
    }

    /**
     * @dataProvider providerGetFileThrowsExceptionWhenInvalidArgumentProvided
     * @expectedException zibo\ZiboException
     */
    public function testGetFileThrowsExceptionWhenInvalidArgumentProvided($value) {
        $this->browser->getFile($value);
    }

    /**
     * @dataProvider providerGetFileThrowsExceptionWhenInvalidArgumentProvided
     * @expectedException zibo\ZiboException
     */
    public function testGetFilesThrowsExceptionWhenInvalidArgumentProvided($value) {
        $this->browser->getFiles($value);
    }

    /**
     * @dataProvider providerGetFile
     */
    public function testGetFile($expected, $value) {
        $result = $this->browser->getFile($value);
        $this->assertEquals($expected, $result);
    }

    public function providerGetFile() {
        $systemPath = realpath(dirname(__FILE__) . '/../../../../../../');
        $file = 'test/src/zibo/library/filesystem/browser/GenericBrowserTest.php';

        return array(
            array(null, 'unexistantFile'),
            array(new File($systemPath, $file), $file),
        );
    }

    /**
     * @dataProvider providerGetFiles
     */
    public function testGetFiles($expected, $value) {
        $result = $this->browser->getFiles($value);
        $this->assertEquals($expected, $result);
    }

    public function providerGetFiles() {
        $systemPath = realpath(dirname(__FILE__) . '/../../../../../../');
        $file = 'test/src/zibo/library/filesystem/browser/GenericBrowserTest.php';

        $resultFile = new File($systemPath, $file);

        return array(
            array(array(), 'unexistantFile'),
            array(array($resultFile->getAbsolutePath() => $resultFile), $file),
        );
    }

}