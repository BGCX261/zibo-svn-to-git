<?php

namespace zibo\library\orm\definition\field;

use zibo\test\BaseTestCase;

class ModelFieldTest extends BaseTestCase {

    public function testSetLabel() {
        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\ModelField', array('name', 'model'));

        $field->setLabel('label');
		$this->assertEquals('label', $field->getLabel());

		$field->setLabel(null);
        $this->assertNull($field->getLabel());
    }

    /**
     * @dataProvider providerSetLabelThrowsExceptionWhenLabelIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testSetLabelThrowsExceptionWhenLabelIsInvalid($value) {
        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\ModelField', array('name', 'model'));
        $field->setLabel($value);
    }

    public function providerSetLabelThrowsExceptionWhenLabelIsInvalid() {
        return array(
            array(''),
            array($this),
            array(array()),
        );
    }

    /**
     * @dataProvider providerSetIsLocalizedThrowsExceptionWhenFlagIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testSetIsLocalizedThrowsExceptionWhenFlagIsInvalid($flag) {
        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\ModelField', array('name', 'model'));
        $field->setIsLocalized($flag);
    }

    public function providerSetIsLocalizedThrowsExceptionWhenFlagIsInvalid() {
        return array(
            array(''),
            array('test'),
            array($this),
            array(array()),
        );
    }

}