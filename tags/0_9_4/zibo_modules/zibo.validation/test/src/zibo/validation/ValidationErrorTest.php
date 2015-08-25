<?php

namespace zibo\library\validation;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ValidationErrorTest extends BaseTestCase {

    /**
     * @dataProvider providerToString
     */
    public function testToString($code, $message, array $parameters, $expected) {
        $error = new ValidationError($code, $message, $parameters);
        $this->assertEquals($expected, $error->__toString(), $code);
    }

    public function providerToString() {
        return array(
            array('code', 'Your message', array(), 'Your message'),
            array('code', 'Your message: %message%', array('message' => 'string'), 'Your message: string'),
            array('code', 'You have %item% in %container%', array('item' => 'a cat', 'container' => 'your bag'), 'You have a cat in your bag'),
            array('code', 'You have a %item%', array('item' => $this), 'You have a object'),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidValueProvided
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidCodeProvided($code) {
        new ValidationError($code, 'message');
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidValueProvided
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidMessageProvided($message) {
        new ValidationError('code', $message);
    }

    public function providerConstructThrowsExceptionWhenInvalidValueProvided() {
        return array(
            array(''),
            array(null),
            array($this),
        );
    }

}