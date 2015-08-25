<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class SizeValidatorTest extends BaseTestCase {

    public function testConstructWithMinimum() {
        $minimum = 4;
        $validator = new SizeValidator(array('minimum' => $minimum));

        $validatorMinimum = Reflection::getProperty($validator, 'minimum');

        $this->assertEquals($minimum, $validatorMinimum);
    }

    public function testConstructWithMaximum() {
        $maximum = 8;
        $validator = new SizeValidator(array('maximum' => $maximum));

        $validatorMaximum = Reflection::getProperty($validator, 'maximum');

        $this->assertEquals($maximum, $validatorMaximum);

    }

    public function testConstructWithMinimumAndMaximum() {
        $minimum = 4;
        $maximum = 8;
        $validator = new SizeValidator(array('minimum' => $minimum, 'maximum' => $maximum));

        $validatorMinimum = Reflection::getProperty($validator, 'minimum');
        $validatorMaximum = Reflection::getProperty($validator, 'maximum');

        $this->assertEquals($minimum, $validatorMinimum);
        $this->assertEquals($maximum, $validatorMaximum);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithoutMinimumAndMaximum() {
        new SizeValidator(array('min' => 5));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithInvalidMinimum() {
        new SizeValidator(array('minimum' => 'invalid'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithInvalidMaximum() {
        new SizeValidator(array('maximum' => 'invalid'));
    }

    /**
     * @dataProvider providerTestIsValidWithMinimum
     */
    public function testIsValidWithMinimum($value, $expected) {
        $minimum = 5;
        $code = 'error.validation.minimum.string';
        $message = 'Field has to have at least %minimum% characters';
        $validator = new SizeValidator(array('minimum' => $minimum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array(
               'value' => $value,
               'minimum' => $minimum,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerTestIsValidWithMinimum() {
        return array(
            array(
                'value' => 'test string',
                'expected' => true,
            ),
            array(
                'value' => '40',
                'expected' => false,
            ),
            array(
                'value' => 'long long long long longer string',
                'expected' => true,
            ),
            array(
                'value' => '55555',
                'expected' => true,
            ),
        );
    }

    /**
     * @dataProvider providerTestIsValidWithMaximum
     */
    public function testIsValidWithMaximum($value, $expected) {
        $maximum = 12;
        $code = 'error.validation.maximum.string';
        $message = 'Field cannot have more than %maximum% characters';
        $validator = new SizeValidator(array('maximum' => $maximum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array(
               'value' => $value,
               'maximum' => $maximum,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerTestIsValidWithMaximum() {
        return array(
            array(
                'value' => 'test string',
                'expected' => true,
            ),
            array(
                'value' => '40',
                'expected' => true,
            ),
            array(
                'value' => 'long long long long longer string',
                'expected' => false,
            ),
            array(
                'value' => '121212121212',
                'expected' => true,
            ),
            array(
                'value' => '55555',
                'expected' => true,
            ),
        );
    }

    /**
     * @dataProvider providerTestIsValidWithMinimumAndMaximum
     */
    public function testIsValidWithMinimumAndMaximum($value, $expected) {
        $minimum = 5;
        $maximum = 12;
        $code = 'error.validation.minmax.string';
        $message = 'Field has to have between %minimum% and %maximum% characters';
        $validator = new SizeValidator(array('minimum' => $minimum, 'maximum' => $maximum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array(
                'value' => $value,
                'minimum' => $minimum,
                'maximum' => $maximum,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerTestIsValidWithMinimumAndMaximum() {
        return array(
            array(
                'value' => 'test string',
                'expected' => true,
            ),
            array(
                'value' => '40',
                'expected' => false,
            ),
            array(
                'value' => 'long long long long longer string',
                'expected' => false,
            ),
            array(
                'value' => '121212121212',
                'expected' => true,
            ),
            array(
                'value' => '55555',
                'expected' => true,
            ),
        );
    }

    /**
     * @dataProvider providerTestIsValidArrayWithMinimum
     */
    public function testIsValidArrayWithMinimum($value, $expected) {
        $minimum = 5;
        $code = 'error.validation.minimum.array';
        $message = 'Field has to have at least %minimum% elements';
        $validator = new SizeValidator(array('minimum' => $minimum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array(
               'value' => $value,
               'minimum' => $minimum,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerTestIsValidArrayWithMinimum() {
        return array(
            array(
                'value' => array(1, 2, 3),
                'expected' => false,
            ),
            array(
                'value' => array(1),
                'expected' => false,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13),
                'expected' => true,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5, 6, 7),
                'expected' => true,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5),
                'expected' => true,
            ),
        );
    }

    /**
     * @dataProvider providerTestIsValidArrayWithMaximum
     *
     * @param array $value
     * @param boolean $expected
     */
    public function testIsValidArrayWithMaximum($value, $expected) {

        $maximum = 12;
        $code = 'error.validation.maximum.array';
        $message = 'Field cannot have more than %maximum% elements';
        $validator = new SizeValidator(array('maximum' => $maximum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array(
               'value' => $value,
               'maximum' => $maximum,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerTestIsValidArrayWithMaximum() {
        return array(
            array(
                'value' => array(1, 2, 3),
                'expected' => true,
            ),
            array(
                'value' => array(1),
                'expected' => true,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13),
                'expected' => false,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5, 6, 7),
                'expected' => true,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5),
                'expected' => true,
            ),
        );
    }

    /**
     * @dataProvider providerTestIsValidArrayWithMinimumAndMaximum
     *
     * @param array $value
     * @param boolean $expected
     */
    public function testIsValidArrayWithMinimumAndMaximum($value, $expected) {
        $minimum = 5;
        $maximum = 12;
        $code = 'error.validation.minmax.array';
        $message = 'Field has to have between %minimum% and %maximum% elements';
        $validator = new SizeValidator(array('minimum' => $minimum, 'maximum' => $maximum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $expectedParameters = array(
                'value' => $value,
                'minimum' => $minimum,
                'maximum' => $maximum,
            );
            $expectedErrors = array(new ValidationError($code, $message, $expectedParameters));

            $resultErrors = $validator->getErrors();

            $this->assertEquals($expectedErrors, $resultErrors);
        }
    }

    public function providerTestIsValidArrayWithMinimumAndMaximum() {
        return array(
            array(
                'value' => array(1, 2, 3),
                'expected' => false,
            ),
            array(
                'value' => array(1),
                'expected' => false,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13),
                'expected' => false,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5, 6, 7),
                'expected' => true,
            ),
            array(
                'value' => array(1, 2, 3, 4, 5),
                'expected' => true,
            ),
        );
    }

}