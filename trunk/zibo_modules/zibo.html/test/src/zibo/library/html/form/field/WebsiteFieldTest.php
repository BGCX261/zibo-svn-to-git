<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class WebsiteFieldTest extends BaseTestCase {

    /**
     * @dataProvider providerProcessRequestAddsHttp
     */
    function testProcessRequestAddsHttp($value, $expected) {
        $_REQUEST['website'] = $value;
        $field = new WebsiteField('website');

        $field->processRequest();
        $value = $field->getValue();

        $this->assertEquals($expected, $value);
    }

    public function providerProcessRequestAddsHttp() {
        return array(
            array('www.google.com', 'http://www.google.com'),
            array('http://www.google.com', 'http://www.google.com'),
            array('https://www.google.com', 'https://www.google.com'),
        );
    }

}