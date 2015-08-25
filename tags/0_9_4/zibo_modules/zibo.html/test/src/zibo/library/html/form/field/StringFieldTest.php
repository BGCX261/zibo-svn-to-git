<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class StringFieldTest extends BaseTestCase {

    function testGetHtmlEscapesValueAttributeValue() {
        $field = new StringField('company_name');
        $field->setValue('H&M');
        $html = $field->getHtml();
        $this->assertContains('value="H&amp;M"', $html);
    }
}