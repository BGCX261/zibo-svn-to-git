<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\log\filter\LogItemFilter;
use zibo\log\LogItem;

class DenyFilter implements LogItemFilter {

    public function allowLogItem(LogItem $item) {
        return false;
    }

    public static function createFilterFromConfig(Zibo $zibo, $name, $configBase) {

    }

}