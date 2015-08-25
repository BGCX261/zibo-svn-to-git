<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class WebsiteValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($expected, $value) {
        $validator = new WebsiteValidator();

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result, $value);

        if (!$expected) {
            $regex = Reflection::getProperty($validator, 'regex');

            $expectedParameters = array(
                'value' => $value,
                'regex' => $regex
            );
            $expectedError = new ValidationError(WebsiteValidator::CODE, WebsiteValidator::MESSAGE, $expectedParameters);

            $resultErrors = $validator->getErrors();

            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
           array(true, 'http://www.google.com'),
           array(false, 'www.google.com'),
        );
    }

}