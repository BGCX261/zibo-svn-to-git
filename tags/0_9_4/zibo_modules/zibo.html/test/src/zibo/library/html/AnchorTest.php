<?php

namespace zibo\library\html;

use zibo\test\BaseTestCase;

class AnchorTest extends BaseTestCase {

    public function testConstruct() {
        $label = 'label';
        $href = '#';

        $anchor = new Anchor($label);

        $this->assertEquals($label, $anchor->getLabel());
        $this->assertEquals($href, $anchor->getHref());
    }

}