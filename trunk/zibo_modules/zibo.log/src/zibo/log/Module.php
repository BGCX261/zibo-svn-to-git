<?php

/**
 * @package zibo-log
 */
namespace zibo\log;

use zibo\core\Dispatcher;
use zibo\core\Zibo;

use zibo\library\filesystem\Formatter;

use zibo\log\listener\LogListenerFactory;
use zibo\log\LogItem;
use zibo\log\Log;

use zibo\ZiboException;

/**
 * Log manager
 */
class Module {

    const CLASS_FILTER = 'zibo\\log\\filter\\LogItemFilter';
    const CLASS_LISTENER = 'zibo\\log\\listener\\LogListener';

    const METHOD_CREATE_FILTER = 'createFilterFromConfig';
    const METHOD_CREATE_LISTENER = 'createListenerFromConfig';

    const CONFIG_LOG = 'log';

    const CONFIG_FILTER = 'filter';
    const CONFIG_LISTENER = 'listener';

    const CONFIG_FILTER_ALL = 'all';
    const CONFIG_INVERT = 'invert';
    const CONFIG_TYPE = 'type';

    const LOG_NAME = 'zibo';
    const LOG_INTRO = '--------------------------------------------------------------------';

    private $log;

    public function __construct() {
        $this->log = new Log();
    }

    public function initialize() {
        $zibo = Zibo::getInstance();

        $this->loadListeners($zibo);
        $this->registerEvents($zibo);

        $this->logItem(self::LOG_INTRO);
    }

    public function logItem($title, $message = null, $type = LogItem::INFORMATION, $name = null) {
        if (!$name) {
            $name = self::LOG_NAME;
        }
        $item = new LogItem($title, $message, $type, $name);
        $this->log->addLogItem($item);
    }

    private function registerEvents(Zibo $zibo) {
        $zibo->registerEventListener(Zibo::EVENT_LOG, array($this, 'logItem'));
    }

    private function loadListeners(Zibo $zibo) {
        $config = $zibo->getConfigValue(self::CONFIG_LOG);
        if (isset($config[self::CONFIG_LISTENER])) {
            unset($config[self::CONFIG_LISTENER]);
        }
        if (isset($config[self::CONFIG_FILTER])) {
            unset($config[self::CONFIG_FILTER]);
        }

        $listenerFactory = new LogListenerFactory($zibo);

        foreach ($config as $name => $parameters) {
            $listener = $listenerFactory->createListener($name);
            if ($listener != null) {
                $this->log->addLogListener($listener);
            }
        }
    }

}