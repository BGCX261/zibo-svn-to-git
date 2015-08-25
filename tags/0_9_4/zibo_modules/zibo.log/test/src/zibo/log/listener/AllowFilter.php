<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\log\filter\LogItemFilter;
use zibo\log\LogItem;

class AllowFilter implements LogItemFilter {

    public function allowLogItem(LogItem $item) {
        return true;
    }

    public static function createFilterFromConfig(Zibo $zibo, $name, $configBase) {

    }

}