<?php

namespace zibo\library;

use zibo\test\BaseTestCase;

class SystemTest extends BaseTestCase {

    public function testExecute() {
        $string = 'This is a test string.' . "\n\n" . 'We would like to have multiple lines.';
        $output = System::execute('echo \'' . $string . '\'');
        $this->assertEquals($string, $output);
    }

    /**
     * @dataProvider providerExecuteWithInvalidCommandThrowsException
     * @expectedException zibo\ZiboException
     */
    public function testExecuteWithInvalidCommandThrowsException($command) {
        System::execute($command);
    }

    public function providerExecuteWithInvalidCommandThrowsException() {
        return array(
            array(''),
            array(null),
            array($this),
            array(array()),
            array('unexistingCommand'),
        );
    }

}