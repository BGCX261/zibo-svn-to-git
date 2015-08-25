<?php

namespace zibo\library;

use zibo\test\BaseTestCase;

class StringTest extends BaseTestCase {

    /**
     * @dataProvider providerIsString
     */
    public function testIsString($expected, $value) {
        $result = String::isString($value);
        $this->assertEquals($expected, $result);
    }

    public function providerIsString() {
        return array(
            array(true, 'test'),
            array(true, 7),
            array(true, 7.3),
            array(false, array()),
            array(false, $this),
            array(true, ''),
            array(true, '0'),
            array(true, 0),
        );
    }

    /**
     * @dataProvider providerIsStringNotEmpty
     */
    public function testIsStringNotEmpty($expected, $value) {
        $result = String::isString($value, String::NOT_EMPTY);
        $this->assertEquals($expected, $result);
    }

    public function providerIsStringNotEmpty() {
        return array(
            array(false, null),
            array(false, ''),
            array(true, '0'),
            array(true, 0),
            array(true, 'test'),
            array(true, 7),
            array(true, 7.3),
        );
    }

    public function testGenerate() {
        $string = String::generate();
        $this->assertNotNull($string);
        $this->assertTrue(!empty($string));
        $this->assertTrue(strlen($string) == 8);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGenerateThrowsExceptionWhenLengthOfHaystackIsLessThenRequestedLength() {
        String::generate(155);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGenerateThrowsExceptionWhenInvalidLengthProvided() {
        String::generate('test');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGenerateThrowsExceptionWhenInvalidHaystackProvided() {
        String::generate(8, new String());
    }

    /**
     * @dataProvider providerSafeString
     */
    public function testSafeString($expected, $value) {
        $locale = setlocale(LC_ALL, 'en_IE.utf8', 'en_IE', 'en');
        $result = String::safeString($value);
        $this->assertEquals($expected, $result);
    }

    public function providerSafeString() {
        return array(
            array('Simple_test', 'Simple test'),
            array('Internet_Explorer_Pocket', 'Internet Explorer (Pocket)'),
            array('Jefs_book', 'Jef\'s book'),
            array('tEst', '##tEst@|"'),
            array('a-image.jpg', 'a-image.jpg'),
            array('Lets_test_with_some_strange_chars', 'Let\'s test with some stràngé chars'),
        );
    }

}