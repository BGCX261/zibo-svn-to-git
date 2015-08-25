<?php

namespace zibo\library\filesystem;

use zibo\test\BaseTestCase;

use \Exception;

class FormatterTest extends BaseTestCase {

    public function provideArgumentsForTestFormatSize() {
        $tests = array(
            array(
                'value' => 115,
                'expected' => '115 bytes',
            ),
            array(
                'value' => 2048,
                'expected' => '2 Kb',
            ),
            array(
                'value' => 522336,
                'expected' => '510.09 Kb',
            ),
            array(
                'value' => 1024000,
                'expected' => '1000 Kb',
            ),
            array(
                'value' => 1024000,
                'expected' => '1000 Kb',
            ),
        );

        return $tests;
    }

    /**
     * @dataProvider provideArgumentsForTestFormatSize
     */
    public function testFormatSize($value, $expected) {
        $result = Formatter::formatSize($value);
        $this->assertEquals($expected, $result, $value);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testFormatSizeThrowsExceptionWhenNotNumericValuePassed() {
       formatter::formatSize('test');
    }

}