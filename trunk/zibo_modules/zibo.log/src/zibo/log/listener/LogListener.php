<?php

/**
 * @package zibo-log-listener
 */
namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\log\LogItem;

/**
 * Log listener interface
 */
interface LogListener {

    public function addLogItem(LogItem $item);

    public static function createListenerFromConfig(Zibo $zibo, $name, $configBase);

}