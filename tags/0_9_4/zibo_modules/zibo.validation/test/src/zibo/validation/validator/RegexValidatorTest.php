<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class RegexValidatorTest extends BaseTestCase {

    public function testConstructWithRegex() {
        $regex = 'regex';
        $validator = new RegexValidator(array('regex' => $regex));

        $validatorRegex = Reflection::getProperty($validator, 'regex');
        $this->assertEquals($regex, $validatorRegex);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithoutRegex() {
        new RegexValidator(array('reg' => 'regex'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithEmptyRegex() {
        new RegexValidator(array('regex' => ''));
    }

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $expected) {
        $regex = '/regex/';
        $code = 'error.validation.regex';
        $message = 'Field does not match ' . $regex;

        $validator = new RegexValidator(array('regex' => $regex));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result, $value);

        if (!$expected) {
            $expectedParameters = array(
               'value' => $value,
               'regex' => $regex,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
            array('regex', true),
            array('textregextest', true),
            array('textregtest', false),
        );
    }

}