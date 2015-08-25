<?php

namespace zibo\library\optimizer\jsmin;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

class JSMinTest extends BaseTestCase {

    /**
     * @dataProvider providerMinify
     */
    public function testMinify($expected, $source) {
        $result = JSMin::minify($source);
        $this->assertEquals($expected, $result);
    }

    public function providerMinify() {
        return array(
            array("function testFunction(value){var value=1+3;return value;}", "//\n// Test javascript\n//\n/**\n * test comment\n */\nfunction testFunction(value) {\n\tvar value = 1 + 3; //test comment\n\treturn value;\n}\n\n"),
        );
    }

}