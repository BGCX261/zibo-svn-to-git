<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class EmailValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $expected) {
        $validator = new EmailValidator();
        $regex = Reflection::getProperty($validator, 'regex');

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result, $value);

        if (!$expected) {
            $expectedParameters = array(
                'value' => $value,
                'regex' => $regex,
            );
            $expectedError = new ValidationError(EmailValidator::CODE, EmailValidator::MESSAGE, $expectedParameters);

            $resultErrors = $validator->getErrors();

            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
            array('info@google.com', true),
            array('www.google.com', false)
        );
    }

}