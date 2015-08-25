<?php

/**
 * @package zibo-log
 */
namespace zibo\log;

use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Data container of a log item
 */
class LogItem {

    const INFORMATION = 0;
    const ERROR = 1;
    const WARNING = 2;

    private $title;
    private $message;
    private $type;
    private $name;
    private $date;
    private $microtime;
    private $ip;

    public function __construct($title, $message = '', $type = self::INFORMATION, $name = '') {
        $this->setTitle($title);
        $this->setMessage($message);
        $this->setType($type);
        $this->setName($name);

        $this->date = time();
        $this->microtime = '';
        $this->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['argv'][0];
    }

    public function setTitle($title) {
    	if (!$title instanceof Exception && String::isEmpty($title)) {
            throw new ZiboException('Empty title provided');
        }
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setType($type) {
        if (is_null($type) || ($type != self::INFORMATION && $type != self::ERROR && $type != self::WARNING)) {
        	$message = 'Provided type is invalid. Try ' . self::INFORMATION . ' for information, ' . self::ERROR . ' for a error and ' . self::WARNING . ' for a warning';
            throw new ZiboException($message);
        }
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getDate() {
        return $this->date;
    }

    public function setMicrotime($microtime) {
        $this->microtime = $microtime;
    }

    public function getMicrotime() {
        return $this->microtime;
    }

    public function getIP() {
        return $this->ip;
    }

}