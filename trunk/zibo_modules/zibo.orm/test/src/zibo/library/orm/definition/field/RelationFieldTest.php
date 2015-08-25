<?php

namespace zibo\library\orm\definition\field;

use zibo\library\database\definition\Field;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class RelationFieldTest extends BaseTestCase {

    public function testConstruct() {
        $name = 'name';
        $model = 'model';

        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\RelationField', array($name, $model));

        $this->assertEquals($name, Reflection::getProperty($field, 'name'));
        $this->assertEquals(Field::TYPE_FOREIGN_KEY, Reflection::getProperty($field, 'type'));
        $this->assertEquals($model, Reflection::getProperty($field, 'relationModelName'));
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testConstructThrowsModelExceptionWhenEmptyModelNamePassed() {
        $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\RelationField', array('name', ''));
    }

    /**
     * @dataProvider providerConstructThrowsZiboExceptionWhenInvalidModelNamePassed
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsZiboExceptionWhenInvalidModelNamePassed($model) {
        $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\RelationField', array('name', $model));
    }

    public function providerConstructThrowsZiboExceptionWhenInvalidModelNamePassed() {
        return array(
            array(null),
            array($this),
        );
    }

    /**
     * @dataProvider providerSetIsDependantThrowsExceptionWhenNoBooleanPassed
     * @expectedException zibo\ZiboException
     */
    public function testSetIsDependantThrowsExceptionWhenNoBooleanPassed($flag) {
        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\RelationField', array('name', 'model'));
        $field->setIsDependant($flag);
    }

    public function providerSetIsDependantThrowsExceptionWhenNoBooleanPassed() {
        return array(
            array('test'),
            array($this),
        );
    }

    /**
     * @dataProvider providerSetLinkModelName
     */
    public function testSetLinkModelName($name) {
        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\RelationField', array('name', 'model'));
        $field->setLinkModelName($name);

        $this->assertEquals($name, Reflection::getProperty($field, 'linkModelName'));
    }

    public function providerSetLinkModelName() {
        return array(
            array('name'),
            array(null),
        );
    }

    /**
     * @dataProvider providerSetLinkModelNameThrowsExceptionWhenInvalidNamePassed
     * @expectedException zibo\ZiboException
     */
    public function testSetLinkModelNameThrowsExceptionWhenInvalidNamePassed($name) {
        $field = $this->getMockForAbstractClass('zibo\\library\\orm\\definition\\field\\RelationField', array('name', 'model'));
        $field->setLinkModelName($name);
    }

    public function providerSetLinkModelNameThrowsExceptionWhenInvalidNamePassed() {
        return array(
            array(''),
            array($this),
        );
    }

}