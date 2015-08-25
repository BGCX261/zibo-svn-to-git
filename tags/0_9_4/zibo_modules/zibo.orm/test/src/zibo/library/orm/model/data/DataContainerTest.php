<?php

namespace zibo\library\orm\model\data;

use zibo\test\BaseTestCase;

class DataTest extends BaseTestCase {

    public function testIsset() {
        $data = new Data();

        $this->assertFalse(isset($data->id));
        $this->assertFalse(isset($data->test));

        $data->id = null;
        $data->test = null;

//        $this->assertTrue(isset($data->id));
        $this->assertTrue(isset($data->test));

        unset($data->id);
        unset($data->test);

        $this->assertFalse(isset($data->id));
        $this->assertFalse(isset($data->test));

        $data->id = 1;
        $data->test = 'value';

        $this->assertTrue(isset($data->id));
        $this->assertTrue(isset($data->test));
    }

}