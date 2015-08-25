<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class HiddenFieldTest extends BaseTestCase {

    function testGetHtmlDisplaysCustomAttributes() {
        $field = new HiddenField('some_name');
        $field->setAttribute('onclick', 'window.alert(\'test\');');
        $this->assertContains('onclick="', $field->getHtml());
    }
}