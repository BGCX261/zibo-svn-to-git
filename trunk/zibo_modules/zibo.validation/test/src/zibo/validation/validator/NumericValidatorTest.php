<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;

class NumericValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($expected, $value) {
        $validator = new NumericValidator();

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array('value' => $value);
            $expectedError = new ValidationError(NumericValidator::CODE, NumericValidator::MESSAGE, $expectedParameters);

            $resultErrors = $validator->getErrors();

            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
           array(true, 15),
           array(true, '15'),
           array(false, ''),
           array(false, '12test'),
           array(true, '0'),
        );
    }

}