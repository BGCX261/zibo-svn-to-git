<?php

namespace zibo\log\filter;

use zibo\core\Zibo;

use zibo\log\LogItem;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

class AbstractInvertLogItemFilterTest extends BaseTestCase {

    public function setUp() {
        $this->filter = new MockInvertLogItemFilter();
    }

    /**
     * @dataProvider providerSetInvertThrowsExceptionWhenInvalidValueProvided
     */
    public function testSetInvertThrowsExceptionWhenInvalidValueProvided($value) {
        try {
            $this->filter->setInvert($value);
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function providerSetInvertThrowsExceptionWhenInvalidValueProvided() {
        return array(
            array('test'),
            array('-50'),
        );
    }

}

class MockInvertLogItemFilter extends AbstractInvertLogItemFilter {

    public function allowLogItem(LogItem $item) {

    }

    public static function createFilterFromConfig(Zibo $zibo, $name, $configBase) {

    }

}