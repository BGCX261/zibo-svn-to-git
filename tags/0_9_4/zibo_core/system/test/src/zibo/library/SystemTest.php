<?php

namespace zibo\library;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

use \Exception;

class SystemTest extends BaseTestCase {

    public function testExecute() {
        $browser = new GenericBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();

        $zibo = Zibo::getInstance($browser, $this->configIOMock);

        $string = 'This is a test string.' . "\n\n" . 'We would like to have multiple lines.';
        $output = System::execute('echo \'' . $string . '\'');
        $this->assertEquals($string, $output);

        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testExecuteWithEmptyCommandThrowsException() {
        try {
            System::execute('');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function testExecuteWithInvalidCommandThrowsException() {
        try {
            System::execute('unknownCommand');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    /**
     * @dataProvider providerUrlExists
     */
    public function testUrlExists($expected, $test) {
        $result = System::urlExists($test, 1);
        $this->assertEquals($expected, $result);
    }

    public function providerUrlExists() {
        return array(
            array(true, 'http://www.google.com'),
            array(false, 'http://www.reallyunexistanturlwehope.com'),
        );
    }

    public function testUrlExistsWithEmptyUrlThrowsException() {
        try {
            System::urlExists('');
            $this->fail();
        } catch (ZiboException $e) {
        }
    }

    public function testUrlExistsWithInvalidUrlThrowsException() {
        try {
            System::urlExists(':/invalid');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

}