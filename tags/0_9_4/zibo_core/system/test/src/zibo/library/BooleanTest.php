<?php

namespace zibo\library;

use zibo\test\BaseTestCase;

class BooleanTest extends BaseTestCase {

    /**
     * @dataProvider providerGetBoolean
     */
    public function testGetBoolean($expected, $value, $message) {
        $this->assertEquals($expected, Boolean::getBoolean($value), $message);
    }

    public function providerGetBoolean() {
        return array(
           array(true, '1', 'string 1'),
           array(true, 1, 'number 1'),
           array(false, 0, 'number 0'),
           array(false, 'FALSE', 'string FALSE'),
           array(true, 'true', 'string true'),
           array(true, true, 'boolean true'),
        );
    }

    /**
     * @dataProvider providerGetBooleanThrowsExceptionWhenNonBooleanValueIsPassed
     * @expectedException zibo\ZiboException
     */
    public function testGetBooleanThrowsExceptionWhenNonBooleanValueIsPassed($value) {
        Boolean::getBoolean($value);
    }

    public function providerGetBooleanThrowsExceptionWhenNonBooleanValueIsPassed() {
        return array(
            array('monkey'),
            array($this),
        );
    }

}