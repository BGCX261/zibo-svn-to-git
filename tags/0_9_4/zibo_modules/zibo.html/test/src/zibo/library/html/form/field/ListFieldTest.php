<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class ListFieldTest extends BaseTestCase {

    function testGetHtmlEscapesOptionKeysAndValues() {
        $field = new ListField('test');
        $field->setOptions(array('f&b' => 'Foo & Bar'));
        $html = $field->getHtml();

        $this->assertContains('<option value="f&amp;b">Foo &amp; Bar</option>', $html);
    }

    function testGetHtmlEscapesOptgroupLabel() {
        $field = new ListField('test');
        $field->setOptions(array('opt 1', 'opt 2'), 'Foo & Bar');
        $field->setOptions(array('opt 3', 'opt 4'), 'Science & Fiction');

        $html = $field->getHtml();
        $this->assertContains('<optgroup label="Foo &amp; Bar">', $html);
        $this->assertContains('<optgroup label="Science &amp; Fiction">', $html);
    }
}