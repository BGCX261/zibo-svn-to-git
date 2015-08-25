<?php

namespace zibo\library\optimizer\cssmin;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

class CSSMinTest extends BaseTestCase {

    /**
     * @dataProvider providerMinify
     */
    public function testMinify($expected, $source) {
        $result = CSSMin::min($source, true);
        $this->assertEquals($expected, $result);
    }

    public function providerMinify() {
        return array(
            array("declaration{height:200%;width:100px;}", "/*\n * Test css\n */\n\ndeclaration    {\n\theight:  200%; /* sme */\nwidth: 100px;  }\n\n"),
            array("declaration{height:200%;width:100px;}", "/*\n * Test css\n */\n@import url(style.css);\ndeclaration    {\n\theight:  200%; /* sme */\nwidth: 100px;  }\n\n"),
        );
    }

}