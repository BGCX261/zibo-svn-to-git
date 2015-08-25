<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class DatepickerFormatConverterTest extends BaseTestCase {

    private $converter;

    public function setUp() {
        $this->converter = new DatepickerFormatConverter();
    }

    /**
     * @dataProvider providerConvertFormatFromPhp
     */
    public function testConvertFormatFromPhp($expected, $format) {
        $result = $this->converter->convertFormatFromPhp($format);
        $this->assertEquals($expected, $result);
    }

    public function providerConvertFormatFromPhp() {
        return array(
            array('d/m/y', 'j/n/y'),
            array('dd/mm/yy', 'd/m/Y'),
            array('D/M/yy', 'D/M/Y'),
            array('DD/MM/yy', 'l/F/Y'),
        );
    }

    /**
     * @dataProvider providerConvertFormatFromPhpThrowsExceptionWhenInvalidFormatPassed
     * @expectedException zibo\ZiboException
     */
    public function testConvertFormatFromPhpThrowsExceptionWhenInvalidFormatPassed($format) {
        $this->converter->convertFormatFromPhp($format);
    }

    public function providerConvertFormatFromPhpThrowsExceptionWhenInvalidFormatPassed() {
        return array(
            array(''),
            array(null),
            array($this),
        );
    }

}