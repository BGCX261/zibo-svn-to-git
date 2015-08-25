<?php

/**
 * @package zibo-library
 */
namespace zibo\library;

use zibo\core\Zibo;

use zibo\ZiboException;

/**
 * Cookie data
 */
class Cookie {

    const CLEARED = '_NULL_';

    private static $instance = null;

    private $domain;
    private $path;

    private function __construct() {
        $this->setPath();
    }

    private function __clone() { }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set($key, $value = null, $expire = 0, $path = null, $domain = null) {
        if ($value == null && isset($_COOKIE[$key])) {
            $this->set($key, self::CLEARED, $expire, $path, $domain);
            return;
        }

        if ($path == null) {
            $path = $this->path;
        }
        if ($domain == null) {
            $domain = $this->domain;
        }

        if (setcookie($key, $value, $expire, $path, $domain)) {
            $_COOKIE[$key] = $value;
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Set cookie ' . $key . ' for path ' . $path . ' on domain ' . $domain, $value);
        } else {
            throw new ZiboException('Could not set cookie ' . $key);
        }
    }

    public function get($key, $default = null) {
        if (isset($_COOKIE[$key]) && $_COOKIE[$key] != self::CLEARED) {
            return $_COOKIE[$key];
        }
        return $default;
    }

    public function reset() {
        $_COOKIE = array();
    }

    private function setPath() {
        $request = Zibo::getInstance()->getRequest();
        list($protocol, $website) = explode('://', $request->getBaseUrl());
        list($this->domain, $path) = explode('/', $website, 2);
        if (strpos($this->domain, ':') !== false) {
            list($this->domain, $port) = explode(':', $this->domain, 2);
        }
        $this->path = '/' . $path;
    }

}