<?php

namespace zibo\library;

use zibo\test\BaseTestCase;

class NumberTest extends BaseTestCase {

    /**
     * @dataProvider providerIsNegative
     */
    public function testIsNegative($expected, $value) {
        $this->assertEquals($expected, Number::isNegative($value));
    }

    public function providerIsNegative() {
        return array(
            array(false, 0),
            array(false, 5),
            array(true, -1),
        );
    }

    /**
     * @dataProvider providerIsNegativeThrowsExceptionWhenNoNumericValueIsPassed
     * @expectedException zibo\ZiboException
     */
    public function testIsNegativeThrowsExceptionWhenNoNumericValueIsPassed($value) {
        Number::isNegative($value);
    }

    public function providerIsNegativeThrowsExceptionWhenNoNumericValueIsPassed() {
        return array(
            array('test'),
            array($this),
        );
    }

    /**
     * @dataProvider providerIsOctal
     */
    public function testIsOctal($expected, $value) {
        $this->assertEquals($expected, Number::isOctal($value));
    }

    public function providerIsOctal() {
        return array(
            array(true, 0),
            array(true, 5),
            array(false, 9),
            array(false, 10923),
            array(true, 10723),
        );
    }

    /**
     * @dataProvider providerIsOctalThrowsExceptionWhenNoNumericValueIsPassed
     * @expectedException zibo\ZiboException
     */
    public function testIsOctalThrowsExceptionWhenNoNumericValueIsPassed($value) {
        Number::isOctal($value);
    }

    public function providerIsOctalThrowsExceptionWhenNoNumericValueIsPassed() {
        return array(
            array('test'),
            array($this),
        );
    }

}