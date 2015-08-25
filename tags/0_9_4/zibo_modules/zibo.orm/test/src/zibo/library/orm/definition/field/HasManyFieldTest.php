<?php

namespace zibo\library\orm\definition\field;

use zibo\test\BaseTestCase;

class HasManyFieldTest extends BaseTestCase {

    public function testSetIndexOn() {
        $field = new HasManyField('name', 'model');

        $field->setIndexOn('otherField');
		$this->assertEquals('otherField', $field->getIndexOn());

		$field->setIndexOn(null);
        $this->assertNull($field->getIndexOn());
    }

    /**
     * @dataProvider providerSetIndexOnThrowsExceptionWhenIndexIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testSetIndexOnThrowsExceptionWhenIndexIsInvalid($value) {
        $field = new HasManyField('name', 'model');
        $field->setIndexOn($value);
    }

    public function providerSetIndexOnThrowsExceptionWhenIndexIsInvalid() {
        return array(
            array(''),
            array($this),
            array(array()),
        );
    }

    public function testSetRelationOrder() {
        $field = new HasManyField('name', 'model');

        $field->setRelationOrder('order');
        $this->assertEquals('order', $field->getRelationOrder());

        $field->setRelationOrder(null);
        $this->assertNull($field->getRelationOrder());
    }

    /**
     * @dataProvider providerSetRelationOrderThrowsExceptionWhenOrderIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testSetRelationOrderThrowsExceptionWhenOrderIsInvalid($value) {
        $field = new HasManyField('name', 'model');
        $field->setRelationOrder($value);
    }

    public function providerSetRelationOrderThrowsExceptionWhenOrderIsInvalid() {
        return array(
            array(''),
            array($this),
            array(array()),
        );
    }

}