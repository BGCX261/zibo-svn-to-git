<?php

namespace zibo\library\html\form;

use zibo\library\html\form\field\HiddenField;
use zibo\library\html\form\field\FileField;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

class FormTest extends BaseTestCase {

    private $form;

    protected function setUp() {
        $this->form = new Form('action', 'name');
    }

    public function testConstructHasNoFields() {
        $fields = $this->form->getFields();

        $this->assertFalse($fields->isEmpty());
    }

    public function testConstructSetsMethodPost() {
        $this->assertEquals('post', $this->form->getMethod());
    }

    public function testGetActionReturnsActionSetWithSetAttribute() {
        $this->form->setAttribute('action', 'test_action');

        $this->assertEquals('test_action', $this->form->getAction());
    }

    public function testGetAttributeReturnsActionSetWithSetAction() {
        $this->form->setAction('test_action');

        $this->assertEquals('test_action', $this->form->getAttribute('action'));
    }

    public function testGetMethodReturnsMethodSetWithSetAttribute() {
        $this->form->setAttribute('method', 'get');

        $this->assertEquals('get', $this->form->getMethod());
    }

    public function testGetAttributeReturnsMethodSetWithSetMethod() {
        $this->form->setMethod('get');

        $this->assertEquals('get', $this->form->getAttribute('method'));
    }

    public function testGetNameReturnsNameSetWithSetAttribute() {
        $this->form->setAttribute('name', 'test_name');

        $this->assertEquals('test_name', $this->form->getName());
    }

    public function testGetAttributeReturnsNameSetWithSetName() {
        $this->form->setName('test_name');

        $this->assertEquals('test_name', $this->form->getAttribute('name'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetMethodWithSomethingElseThanPostOrGetThrowsException() {
        $this->form->setMethod('postl');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetAttributeMethodWithSomethingElseThanPostOrGetThrowsException() {
        $this->form->setAttribute('method', 'postl');
    }

    public function testGetAttributeReturnsAttributeValueSetWithSetAttribute() {
        $this->form->setAttribute('whatever', 'whatever_value');

        $this->assertEquals('whatever_value', $this->form->getAttribute('whatever'));
    }

    public function testAddField() {
        $fieldName = 'test_field';

        $field = $this->getFieldMock($fieldName);

        $this->form->addField($field);

        $this->assertTrue($this->form->hasField($fieldName));
        $this->assertEquals($field, $this->form->getField($fieldName));
    }

    public function testAddFieldSetsEncTypeToMultipartFormDataWhenFieldInstanceOfFileField() {
        $this->form->addField(new FileField('file'));

        $this->assertEquals('multipart/form-data', $this->form->getAttribute('enctype'));
    }

    public function testAddFieldSetsIdOfTheFieldWhenNoIdSet() {
        $field = $this->getFieldMock('test_field');

        $this->form->addField($field);

        $this->assertEquals('nameTest_field', $field->getId());
    }

    public function testHasFieldReturnsFalseWhenFieldDoesNotExist() {
        $this->assertSame(false, $this->form->hasField('non_existing_field'));
    }

    public function testHasFieldReturnsTrueWhenFieldExist() {
        $field = $this->getFieldMock('test_field');

        $this->form->addField($field);

        $this->assertSame(true, $this->form->hasField('test_field'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetIsDisabledWithUnknownFieldNameThrowsException() {
        $this->form->setIsDisabled(true, 'non_existing_field');
    }

    public function testSetIsDisabledWithKnownFieldNameCallsIsDisabledOnField() {
        $field = $this->getFieldMock('test_field');
        $field->expects($this->once())
              ->method('setIsDisabled')
              ->with($this->equalTo(true));

        $this->form->addField($field);

        $this->form->setIsDisabled(true, 'test_field');
    }

    public function testSetIsDisabledWithoutFieldNameCallsIsDisabledOnAllFields() {
        $fieldA = $this->getFieldMock('test_field_a', array('setIsDisabled'));
        $fieldA->expects($this->once())
               ->method('setIsDisabled')
               ->with($this->equalTo(true));

        $fieldB = $this->getFieldMock('test_field_b', array('setIsDisabled'));
        $fieldB->expects($this->once())
               ->method('setIsDisabled')
               ->with($this->equalTo(true));

        $this->form->addField($fieldA);
        $this->form->addField($fieldB);

        $this->form->setIsDisabled(true);
    }

    public function testProcessRequestCallsProcesRequestOnAllFields() {
        $fieldA = $this->getFieldMock('test_field_a', array('processRequest'));
        $fieldA->expects($this->once())
               ->method('processRequest');

        $fieldB = $this->getFieldMock('test_field_b', array('processRequest'));
        $fieldB->expects($this->once())
               ->method('processRequest');

        $this->form->addField($fieldA);
        $this->form->addField($fieldB);

        $this->form->processRequest();
    }

    public function testProcessRequestDoesNotCallProcessRequestOnAllFieldsAgainOnMultipleCalls() {
        $fieldA = $this->getFieldMock('test_field_a', array('processRequest'));
        $fieldA->expects($this->once())
               ->method('processRequest');

        $fieldB = $this->getFieldMock('test_field_b', array('processRequest'));
        $fieldB->expects($this->once())
               ->method('processRequest');

        $this->form->addField($fieldA);
        $this->form->addField($fieldB);

        $this->form->processRequest();
        $this->form->processRequest();
    }

    public function testProcessRequestSkipsDisabledFields() {
        $fieldA = $this->getFieldMock('test_field_a', array('processRequest'));
        $fieldA->expects($this->once())
               ->method('processRequest');

        $fieldB = $this->getFieldMock('test_field_b', array('processRequest'));
        $fieldB->setIsDisabled(true);
        $fieldB->expects($this->never())
               ->method('processRequest');

        $this->form->addField($fieldA);
        $this->form->addField($fieldB);

        $this->form->processRequest();
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testAddValidatorWithUnknownFieldNameThrowsException() {
        $validator = $this->getMock('zibo\\library\\html\form\ValidatorMock');
        $this->form->addValidator('non_existing_field', $validator);
    }

    public function testGetFieldWithoutArgumentGivesTheFormSubmitField() {
        $field = $this->form->getField();

        $this->assertTrue($field instanceof HiddenField);
        $this->assertEquals(Form::SUBMIT_NAME . 'name', $field->getName());
    }

    /**
     * @expectedException zibo\ziboException
     */
    public function testGetFieldWithUnknownFieldNameThrowsException() {
        $field = $this->form->getField('non_existing_field');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGetValueWithUnknownFieldNameThrowsException() {
        $this->form->getValue('non_existing_field');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetValueWithUnknownFieldNameThrowsException() {
        $this->form->setValue('non_existing_field', 'whatever_value');
    }

    private function getFieldMock($name = 'name', $methods = array('processRequest', 'setIsDisabled')) {
        return $this->getMock('zibo\library\html\form\FieldMock', $methods, array($name));
    }

}