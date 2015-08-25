<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DsnValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($expected, $test) {
        $validator = new DsnValidator();

        $result = $validator->isValid($test);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $resultErrors = $validator->getErrors();

            $regex = Reflection::getProperty($validator, 'regex');
            $expectedParameters = array(
                'value' => $test,
                'regex' => $regex,
            );
            $expectedErrors = array(new ValidationError(DsnValidator::CODE, DsnValidator::MESSAGE, $expectedParameters));

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
            array(true, 'mysql://localhost/database'),
            array(true, 'mysql://username:password@localhost:3306/database'),
            array(false, 'mysql://username:password@localhost:3306//database'),
            array(false, 'mysql://username:password@localhost:3306'),
            array(false, 'mysql://username:password@localhost:3306/'),
            array(false, 'www.google.com'),
            array(true, 'sqlite://tmp/file.db'),
            array(true, 'sqlite:///var/lib/sqlite/file.db'),
        );
    }

}