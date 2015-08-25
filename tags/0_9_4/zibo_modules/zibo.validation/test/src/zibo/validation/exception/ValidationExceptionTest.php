<?php

namespace zibo\library\validation\exception;

use zibo\library\validation\ValidationError;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ValidationExceptionTest extends BaseTestCase {

    private $exception;

    public function setUp() {
        $this->exception = new ValidationException();
    }

    public function testAddErrors() {
        $name = 'field';
        $errors = array(
            new ValidationError('code1', 'message1'),
            new ValidationError('code2', 'message2'),
        );

        $this->exception->addErrors($name, $errors);

        $exceptionErrors = Reflection::getProperty($this->exception, 'errors');

        $this->assertEquals(array($name => $errors), $exceptionErrors);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testAddErrorsThrowsExceptionWhenNoValidationErrorPassed() {
        $name = 'field';
        $errors = array($this);

        $this->exception->addErrors($name, $errors);
    }

    public function testHasErrors() {
        $this->assertEquals(false, $this->exception->hasErrors());

        $this->exception->addErrors('field1', array());

        $this->assertEquals(false, $this->exception->hasErrors());

        $this->exception->addErrors('field2', array(new ValidationError('code', 'message')));

        $this->assertEquals(true, $this->exception->hasErrors());
    }

    public function testGetErrors() {
        $name1 = 'field1';
        $errors1 = array(
            new ValidationError('code1', 'message1'),
            new ValidationError('code2', 'message2'),
        );

        $name2 = 'field2';
        $errors2 = array(
            new ValidationError('code3', 'message3'),
            new ValidationError('code4', 'message4'),
        );

        $this->exception->addErrors($name1, $errors1);
        $this->exception->addErrors($name2, $errors2);

        $this->assertEquals($errors1, $this->exception->getErrors($name1));
        $this->assertEquals($errors2, $this->exception->getErrors($name2));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGetErrorsThrowsExceptionWhenFieldHasNoErrors() {
        $this->exception->getErrors('field');
    }

    public function testGetAllErrors() {
        $name1 = 'field1';
        $errors1 = array(
            new ValidationError('code1', 'message1'),
            new ValidationError('code2', 'message2'),
        );

        $name2 = 'field2';
        $errors2 = array(
            new ValidationError('code3', 'message3'),
            new ValidationError('code4', 'message4'),
        );

        $this->exception->addErrors($name1, $errors1);
        $this->exception->addErrors($name2, $errors2);

        $expectedErrors = array(
            $name1 => $errors1,
            $name2 => $errors2,
        );

        $this->assertEquals($expectedErrors, $this->exception->getAllErrors());
    }

    public function testGetErrorsAsString() {
        $this->assertEquals('', $this->exception->getErrorsAsString());

        $name1 = 'field1';
        $errors1 = array(
            new ValidationError('code1', 'message1'),
        );
        $this->exception->addErrors($name1, $errors1);

        $expected = '<ul><li>Field1: message1</li>';

        $this->assertEquals($expected . '</ul>', $this->exception->getErrorsAsString());

        $name2 = 'field2';
        $errors2 = array(
            new ValidationError('code3', 'message3'),
            new ValidationError('code4', 'message4'),
        );
        $this->exception->addErrors($name2, $errors2);

        $expected .= '<li>Field2: <ul><li>message3</li><li>message4</li></ul></li>';

        $this->assertEquals($expected . '</ul>', $this->exception->getErrorsAsString());
    }

}