<?php

namespace zibo\library\html;

use zibo\ZiboException;

use zibo\test\BaseTestCase;

class AbstractElementTest extends BaseTestCase {

    private $element;

    protected function setUp() {
        $this->element = $this->getMock('zibo\\library\\html\\AbstractElement', array('getHtml'));
    }

    public function testAppendToClass() {
        $this->element->appendToClass('test');
        $this->assertEquals('test', $this->element->getClass());

        $this->element->appendToClass('tester');
        $this->assertEquals('test tester', $this->element->getClass());
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testAppendToClassThrowsExceptionWhenEmptyClassPassed() {
        $this->element->appendToClass('');
    }

    public function testRemoveFromClass() {
        $this->element->appendToClass('test');
        $this->element->appendToClass('tester');

        $this->element->removeFromClass('test');

        $this->assertEquals('tester', $this->element->getClass());
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRemoveFromClassThrowsExceptionWhenEmptyClassPassed() {
        $this->element->removeFromClass('');
    }

    public function testSetAttribute() {
        $this->element->setAttribute('attribute1', 'value1');
        $this->element->setAttribute('attribute2', 'value2');

        $expectedAttributes = array('attribute1' => 'value1', 'attribute2' => 'value2');
        $attributes = $this->element->getAttributes();

        $this->assertEquals($expectedAttributes, $attributes);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetAttributeThrowsExceptionWhenEmptyAttributePassed() {
        $this->element->setAttribute('', 'value');
    }

    public function testAddAttributeSetsId() {
        $this->element->setAttribute('id', 'value1');
        $this->assertEquals('value1', $this->element->getId());
    }

    public function testAddAttributeSetsClass() {
        $this->element->setAttribute('class', 'value1');
        $this->assertEquals('value1', $this->element->getClass());
    }

    public function testGetAttributeGivesId() {
        $this->element->setId('value1');
        $this->assertEquals('value1', $this->element->getAttribute('id'));
    }

    public function testGetAttributeGivesClass() {
        $this->element->setClass('value1');
        $this->assertEquals('value1', $this->element->getAttribute('class'));
    }

    public function testGetAttribute() {
        $this->element->setAttribute('attribute1', 'value1');
        $this->assertEquals('value1', $this->element->getAttribute('attribute1'));
        $this->assertEquals('value2', $this->element->getAttribute('attribute2', 'value2'));
    }
}