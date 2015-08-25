<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;

class RequiredValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($value, $expected) {
        $code = 'error.validation.required';
        $message = 'Field is required';
        $validator = new RequiredValidator();

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result, $value);

        if (!$expected) {
            $expectedParameters = array(
               'value' => $value
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
            array(15, true),
            array(87, true),
            array(0, true),
            array('', false),
            array('test', true),
            array('0', true),
            array(null, false),
        );
    }

}