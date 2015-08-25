<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ClassValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($expected, $test, $options) {
        $validator = new ClassValidator($options);

        $result = $validator->isValid($test);
        $this->assertEquals($expected, $result);

        if (!$expected) {
//            $resultErrors = $validator->getErrors();
//
//            $regex = Reflection::getProperty($validator, 'regex');
//            $expectedParameters = array(
//                'value' => $test,
//                'regex' => $regex,
//            );
//            $expectedErrors = array(new ValidationError(DsnValidator::CODE, DsnValidator::MESSAGE, $expectedParameters));
//
//            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
            array(false, 'invalid class name', array()),
            array(true, '', array(ClassValidator::OPTION_REQUIRED => 0)),
            array(false, '', array(ClassValidator::OPTION_REQUIRED => 1)),
            array(true, 'zibo\\library\\validation\\validator\\ClassValidatorTest', array()),
            array(true, 'zibo\\library\\validation\\validator\\ClassValidator', array(ClassValidator::OPTION_CLASS => 'zibo\\library\validation\\validator\\Validator')),
            array(false, 'zibo\\library\\validation\\validator\\ClassValidatorTest', array(ClassValidator::OPTION_CLASS => 'zibo\\library\validation\\validator\\Validator')),
            array(true, 'zibo\\library\\validation\\validator\\WebsiteValidator', array(ClassValidator::OPTION_CLASS => 'zibo\\library\validation\\validator\\RegexValidator')),
        );
    }

}