<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class AbstractFieldTest extends BaseTestCase {

    const TEXT = '&<>"\'';
    const TEXT_ATTRIBUTE_ESCAPED = '&amp;&lt;&gt;&quot;\'';

    function testGetDisplayValueHtmlEscapesAttributeValue() {
        $field = new TestAbstractField('test');
        $field->setValue(self::TEXT);
        $html = $field->getDisplayValueHtml();
        $this->assertContains(self::TEXT_ATTRIBUTE_ESCAPED, $html);
    }

    function testGetNameHtmEscapesAttributeValue() {
        $field = new TestAbstractField('test');
        $field->setName(self::TEXT);
        $this->assertContains(self::TEXT_ATTRIBUTE_ESCAPED, $field->getNameHtml());
    }

    function testGetNameReturnsNameAttribute() {
        $field = new TestAbstractField('initial_name');
        $field->setAttribute('name', 'other_name');
        $this->assertEquals('other_name', $field->getName());
    }
}

class TestAbstractField extends AbstractField {

    public function getDisplayValueHtml() {
        return parent::getDisplayValueHtml();
    }

    public function getNameHtml() {
        return parent::getNameHtml();
    }

    /**
     * Just a placeholder so that the Element interface is implemented correctly
     */
    public function getHtml() {

    }
}