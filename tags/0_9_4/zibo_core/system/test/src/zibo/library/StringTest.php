<?php

namespace zibo\library;

use zibo\test\BaseTestCase;

class StringTest extends BaseTestCase {

    public function testIsEmtpy() {
        $this->assertTrue(String::isEmpty(''), 'Empty string is not empty, it should be');
        $this->assertFalse(String::isEmpty('0'), 'String with 0 is empty, it shouldn\'t be');
        $this->assertFalse(String::isEmpty(0), 'Number 0 is empty, shouldn\'t be');
        $this->assertTrue(String::isEmpty(null), 'null is not empty, it should be');
    }

    /**
     * @dataProvider providerIsEmptyThrowsExceptionInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testIsEmptyThrowsExceptionWhenInvalidValuePassed($value) {
        String::isEmpty(array('value'));
    }

    public function providerIsEmptyThrowsExceptionInvalidValuePassed() {
        return array(
            array(array('value')),
            array(new StringTest()),
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
     * @dataProvider providerLooksLikeUrl
     */
    public function testLooksLikeUrl($expected, $value) {
        $result = String::looksLikeUrl($value);
        $this->assertEquals($expected, $result);
    }

    public function providerLooksLikeUrl() {
        return array(
            array(false, 'Simple test'),
            array(false, 'www.google.com'),
            array(true, 'http://www.google.com'),
            array(true, 'https://www.google.com'),
        );
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