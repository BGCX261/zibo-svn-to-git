<?php

/**
 * @package zibo-log-filter
 */
namespace zibo\log\filter;

use zibo\core\Zibo;

use zibo\log\LogItem;

/**
 * Interface to filter log items
 */
interface LogItemFilter {

	public function allowLogItem(LogItem $item);

    public static function createFilterFromConfig(Zibo $zibo, $name, $configBase);

}