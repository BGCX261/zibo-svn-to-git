<?php

namespace zibo\library\validation\validator;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class FileExtensionValidatorTest extends BaseTestCase {

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($expected, $extensions) {
        $validator = new FileExtensionValidator($this->getArguments($extensions));

        $validatorExtensions = Reflection::getProperty($validator, 'extensions');
        $this->assertEquals($expected, $validatorExtensions);
    }

    public function providerConstruct() {
        return array(
           array(array(), null),
           array(array('txt' => 'txt'), 'txt'),
           array(array('txt' => 'txt', 'jpg' => 'jpg', 'gif' => 'gif'), 'txt,jpg, gif'),
        );
    }

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($expected, $value, $extensions, $required = true, $errorCode = FileExtensionValidator::CODE, $errorMessage = FileExtensionValidator::MESSAGE) {
        $validator = new FileExtensionValidator($this->getArguments($extensions, $required));

        $result = $validator->isValid($value);
        $this->assertEquals($expected, $result);

        if (!$expected) {
            if ($errorCode == RequiredValidator::CODE) {
                $expectedParameters = array();
            } else {
                $expectedParameters = array(
                    'value' => $value,
                    'extensions' => $extensions
                );
            }
            $expectedError = new ValidationError($errorCode, $errorMessage, $expectedParameters);

            $resultErrors = $validator->getErrors();

            $this->assertEquals(array($expectedError), $resultErrors);
        }
    }

    public function providerIsValid() {
        return array(
           array(true, 'test.txt', null),
           array(false, 'test', null),
           array(true, '', null, false),
           array(false, '', null, true, RequiredValidator::CODE, RequiredValidator::MESSAGE),
           array(false, null, 'txt', true, RequiredValidator::CODE, RequiredValidator::MESSAGE),
           array(true, 'test.txt', 'txt'),
           array(false, 'test.txt', 'jpg'),
           array(true, 'test.txt', 'jpg,gif,txt'),
        );
    }

    private function getArguments($extensions, $required = null) {
        $arguments = array();

        if ($extensions) {
            $arguments[FileExtensionValidator::OPTION_EXTENSIONS] = $extensions;
        }

        if ($required !== null) {
            $arguments[FileExtensionValidator::OPTION_REQUIRED] = $required;
        }

        return $arguments;
    }

}