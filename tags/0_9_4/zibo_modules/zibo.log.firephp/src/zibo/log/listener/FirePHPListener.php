<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\log\LogItem;

use \Exception;
use \FirePHP;

class FirePHPListener extends AbstractFilteredLogListener {

	private $firePHP;

    private $logItems = array();

    private $typeMapping = array(
        LogItem::INFORMATION => FirePHP::INFO,
        LogItem::ERROR => FirePHP::ERROR,
        LogItem::WARNING => FirePHP::WARN,
    );

    public function __construct() {
    	$this->firePHP = FirePHP::getInstance(true);
    }

    protected function writeLogItem(LogItem $item) {
        $type = $this->getFirePHPType($item);
        $title = $item->getMicrotime() . ' [' . $item->getName() . ']';

        $message = $item->getTitle();
        $itemMessage = $item->getMessage();
        if ($itemMessage !== NULL) {
            $message .= ' - ' . $itemMessage;
        }

        try {
            $this->firePHP->fb($message, $title, $type);
        } catch (Exception $e) {

        }
    }

    private function getFirePHPType(LogItem $item) {
    	$type = $item->getType();

    	if (isset($this->typeMapping[$type])) {
    	    $type = $this->typeMapping[$type];
    	} else {
    	    $type = FirePHP::LOG;
    	}

        return $type;
    }

    public static function createListenerFromConfig(Zibo $zibo, $name, $configBase) {
    	$listener = new self();

    	self::addFiltersToCreatedListener($listener, $zibo, $name, $configBase);

    	return $listener;
    }

}