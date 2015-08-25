<?php

namespace zibo\library\validation\filter;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class TrimFilterTest extends BaseTestCase {

    /**
     * @dataProvider providerFilter
     */
    public function testFilter($value, $expected) {
        $filter = new TrimFilter();

        $result = $filter->filter($value);
        $this->assertEquals($expected, $result, $value);
    }

    public function providerFilter() {
        return array(
            array(false, false),
            array(true, true),
            array(456, 456),
            array('0', '0'),
            array('info@google.com', 'info@google.com'),
            array('  www.google.com  ', 'www.google.com'),
            array($this, $this),
        );
    }

}