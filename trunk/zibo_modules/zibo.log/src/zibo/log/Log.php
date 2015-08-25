<?php

/**
 * @package zibo-library-log
 */
namespace zibo\log;

use zibo\log\listener\LogListener;

use zibo\library\Timer;

/**
 * Log manager, container of the listeners and delegates log items to the listeners.
 */
class Log {

    private $listeners;
    private $timer;

    public function __construct() {
        $this->listeners = array();
        $this->timer = new Timer();
    }

    public function addLogListener(LogListener $listener) {
        $this->listeners[] = $listener;
    }

    public function addLogItem(LogItem $item) {
        $item->setMicrotime($this->getTime());

        foreach ($this->listeners as $listener) {
            $listener->addLogItem($item);
        }
    }

    public function getTime() {
        return $this->timer->getTime();
    }

}