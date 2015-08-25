<?php

namespace zibo\library\i18n\locale;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class LocaleTest extends BaseTestCase {

    public function testConstruct() {
        $code = "code";
        $name = "name";
        $plural = "plural";
        $locale = new Locale($code, $name, $plural);

        $this->assertEquals($code, $locale->getCode());
        $this->assertEquals($name, $locale->getName());
        $this->assertEquals($plural, $locale->getPluralScript());
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidValuePassed($code, $name, $plural) {
        new Locale($code, $name, $plural);
    }

    public function providerConstructThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array('code', '', 'plural'),
            array('', 'name', 'plural'),
        );
    }

    public function testSetDateFormat() {
        $identifier = 'id';
        $format = 'd-m-Y';

        $locale = new Locale('en', 'en');
        $locale->setDateFormat($identifier, $format);

        $expected = array(
            $identifier => $format,
        );

        $this->assertEquals($expected, Reflection::getProperty($locale, 'dateFormats'));
    }

    /**
     * @dataProvider providerSetDateFormatThrowsExceptionWithInvalidIdentifier
     * @expectedException zibo\ZiboException
     */
    public function testSetDateFormatThrowsExceptionWithInvalidIdentifier($identifier) {
        $locale = new Locale('en', 'en');
        $locale->setDateFormat($identifier, 'd-m-Y');
    }

    public function providerSetDateFormatThrowsExceptionWithInvalidIdentifier() {
        return array(
            array(''),
            array($this),
        );
    }

    /**
     * @dataProvider providerSetDateFormatThrowsExceptionWithInvalidFormat
     * @expectedException zibo\ZiboException
     */
    public function testSetDateFormatThrowsExceptionWithInvalidFormat($format) {
        $locale = new Locale('en', 'en');
        $locale->setDateFormat('id', $format);
    }

    public function providerSetDateFormatThrowsExceptionWithInvalidFormat() {
        return array(
            array(''),
            array($this),
        );
    }

    public function testGetDateFormat() {
        $identifier = 'id';
        $format = 'd-m-Y';

        $locale = new Locale('en', 'en');
        $locale->setDateFormat($identifier, $format);

        $result = $locale->getDateFormat($identifier);

        $this->assertEquals($format, $result);
    }

    public function testGetDateFormatReturnsIdentifierWhenFormatNotFound() {
        $locale = new Locale('en', 'en');
        $identifier = 'id';

        $result = $locale->getDateFormat($identifier);

        $this->assertEquals($result, $identifier);
    }

    public function testGetDateFormatReturnsDefaultDateFormat() {
        $locale = new Locale('en', 'en');

        $result = $locale->getDateFormat();

        $this->assertEquals($result, Locale::DEFAULT_DATE_FORMAT);
    }

    /**
     * @dataProvider providerFormatDate
     */
    public function testFormatDate($expected, $date, $formatIdentifier) {
        $identifier = 'id';
        $format = 'd-m-Y';

        $locale = new Locale('en', 'en');
        $locale->setDateFormat($identifier, $format);

        $result = $locale->formatDate($date, $formatIdentifier);

        $this->assertEquals($expected, $result);
    }

    public function providerFormatDate() {
        $time = time();
        return array(
            array(date('d-m-Y', $time), $time, 'id'),
            array(date(Locale::DEFAULT_DATE_FORMAT, $time), $time, null),
        );
    }

    /**
     * @dataProvider providerParseDate
     */
    public function testParseDate($expected, $date, $formatIdentifier) {
        $identifier = 'id';
        $format = 'd-m-Y';

        $locale = new Locale('en', 'en');
        $locale->setDateFormat($identifier, $format);

        $result = $locale->parseDate($date, $formatIdentifier);
        $resultDateParts = getdate($result);
        $expectedDateParts = getdate($expected);

        $this->assertEquals($expectedDateParts['mday'], $resultDateParts['mday']);
        $this->assertEquals($expectedDateParts['mon'], $resultDateParts['mon']);
        $this->assertEquals($expectedDateParts['year'], $resultDateParts['year']);
    }

    public function providerParseDate() {
        $time = time() - (60 * 60 * 24 * 15);

        return array(
            array($time, date('d-m-Y', $time), 'id'),
            array($time, date(Locale::DEFAULT_DATE_FORMAT, $time), null),
        );
    }

    public function testGetDateFormatsReturnsAvailableDateFormats() {
        $locale = new Locale('en', 'en');
        $locale->setDateFormat('id', 'd-m-Y');

        $dateFormats = $locale->getDateFormats();
        $this->assertTrue(is_array($dateFormats));

        $this->assertEquals(array('id' => 'd-m-Y'), $dateFormats);
    }

}