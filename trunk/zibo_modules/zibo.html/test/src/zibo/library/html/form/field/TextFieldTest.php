<?php

namespace zibo\library\html\form\field;

use zibo\test\BaseTestCase;

class TextFieldTest extends BaseTestCase {

    function testGetHTMLEscapesContent() {
        $field = new TextField('comment');
        $content = '<strong>some markup in a text area</strong> & some more \'"';
        $expected = '&lt;strong&gt;some markup in a text area&lt;/strong&gt; &amp; some more \'"';

        $field->setValue($content);
        $html = $field->getHtml();

        $this->assertContains($expected, $html);
    }
}