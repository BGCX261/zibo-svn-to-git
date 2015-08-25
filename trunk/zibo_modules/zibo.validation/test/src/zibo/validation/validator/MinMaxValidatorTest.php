<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class MinMaxValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($minimum, $maximum) {
        $options = array();
        if ($minimum !== null) {
            $options[MinMaxValidator::OPTION_MINIMUM] = $minimum;
        }
        if ($maximum !== null) {
            $options[MinMaxValidator::OPTION_MAXIMUM] = $maximum;
        }
        $validator = new MinMaxValidator($options);

        $validatorMinimum = Reflection::getProperty($validator, 'minimum');
        $validatorMaximum = Reflection::getProperty($validator, 'maximum');
        $this->assertEquals($minimum, $validatorMinimum);
        $this->assertEquals($maximum, $validatorMaximum);
    }

    public function providerConstruct() {
        return array(
           array(4, null),
           array(null, 8),
           array(5, 8),
        );
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithoutMinimumAndMaximum() {
        new MinMaxValidator(array('unused' => 5));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithInvalidMinimum() {
        new MinMaxValidator(array(MinMaxValidator::OPTION_MINIMUM => 'invalid'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWithInvalidMaximum() {
        new MinMaxValidator(array(MinMaxValidator::OPTION_MAXIMUM => 'invalid'));
    }

    /**
     * @dataProvider providerIsValidWithMinimum
     */
    public function testIsValidWithMinimum($expected, $value, $minimum) {
        $validator = new MinMaxValidator(array(MinMaxValidator::OPTION_MINIMUM => $minimum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $resultErrors = $validator->getErrors();
            $expectedParameters = array(
                'value' => $value,
                'minimum' => $minimum,
            );
            $expectedError = new ValidationError(MinMaxValidator::CODE_MINIMUM, MinMaxValidator::MESSAGE_MINIMUM, $expectedParameters);
            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValidWithMinimum() {
        return array(
           array(false, 15, 20),
           array(true, '40', 20),
           array(true, '100', 20),
           array(true, '20', 20),
           array(false, '19', 20),
        );
    }

    /**
     * @dataProvider providerIsValidWithMaximum
     */
    public function testIsValidWithMaximum($expected, $value, $maximum) {
        $validator = new MinMaxValidator(array(MinMaxValidator::OPTION_MAXIMUM => $maximum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $resultErrors = $validator->getErrors();
            $expectedParameters = array(
                'value' => $value,
                'maximum' => $maximum,
            );
            $expectedError = new ValidationError(MinMaxValidator::CODE_MAXIMUM, MinMaxValidator::MESSAGE_MAXIMUM, $expectedParameters);
            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValidWithMaximum() {
        return array(
           array(true, 15, 50),
           array(true, '40', 50),
           array(false, '223.56', 50),
           array(false, '100', 50),
           array(true, '20', 50),
           array(true, '50', 50),
        );
    }

    /**
     * @dataProvider providerIsValidWithMinimumAndMaximum
     */
    public function testIsValidWithMinimumAndMaximum($expected, $value, $minimum, $maximum) {
        $validator = new MinMaxValidator(array(MinMaxValidator::OPTION_MINIMUM => $minimum, MinMaxValidator::OPTION_MAXIMUM => $maximum));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $resultErrors = $validator->getErrors();
            $expectedParameters = array(
                'value' => $value,
                'minimum' => $minimum,
                'maximum' => $maximum,
            );
            $expectedError = new ValidationError(MinMaxValidator::CODE_MINMAX, MinMaxValidator::MESSAGE_MINMAX, $expectedParameters);
            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValidWithMinimumAndMaximum() {
        return array(
           array(false, 15, 20, 50),
           array(true, '40', 20, 50),
           array(false, '223.56', 20, 50),
           array(false, '100', 20, 50),
           array(true, '20', 20, 50),
           array(true, '50', 20, 50),
           array(false, '-15', 0, 50),
        );
    }

}