<?php

namespace zibo\library\spider;

use zibo\test\BaseTestCase;

class SpiderStatusTest extends BaseTestCase {

    /**
     * @dataProvider providerGetElapsedTime
     */
    public function testGetElapsedTime($expected, $start, $stop) {
        $status = new SpiderStatus(null, 0, 0, $start, $stop);

        $result = $status->getElapsedTime();

        $this->assertEquals($expected, $result);
    }

    public function providerGetElapsedTime() {
        return array(
            array('00:00:01', 5, 6),
            array('00:02:01', 1, 122),
            array('01:02:13', 1, 3734),
        );
    }

}