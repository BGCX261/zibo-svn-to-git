<?php

namespace zibo\library\cli;

use zibo\test\BaseTestCase;

class ArgumentParserTest extends BaseTestCase {

    /**
     * @dataProvider providerParseArguments
     */
    public function testParseArguments(array $expected, array $arguments) {
        $result = ArgumentParser::parseArguments($arguments);
        $this->assertEquals($expected, $result);
    }

    public function providerParseArguments() {
        return array(
            array(
                array(
                    0 => 'admin/system',
                ),
                array(
                	'admin/system',
            	),
        	),
            array(
                array(
                    0 => 'admin/system',
                    'path' => '/path/to/something',
                ),
                array(
                    'admin/system',
                	'--path=/path/to/something',
            	),
        	),
            array(
                array(
                    0 => 'admin/system',
                    'a' => true,
                    'b' => true,
                    'c' => true,
                ),
                array(
                	'admin/system',
                	'-abc',
            	),
        	),
        );
    }

}