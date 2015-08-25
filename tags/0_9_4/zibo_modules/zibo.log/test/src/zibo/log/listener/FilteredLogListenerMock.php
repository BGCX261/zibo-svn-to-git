<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\log\LogItem;

class FilteredLogListenerMock extends AbstractFilteredLogListener {

    private $numItems = 0;

    public function getNumLogItems() {
        return $this->numItems;
    }

    public function writeLogItem(LogItem $item) {
        $this->numItems++;
    }

    public static function createListenerFromConfig(Zibo $zibo, $name, $configBase) {

    }

}