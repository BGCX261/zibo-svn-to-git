<?php

namespace zibo\test;

use zibo\library\filesystem\browser\Browser;
use zibo\library\filesystem\File;

use zibo\core\Zibo;

use \Exception;

/**
 * Autoloader for the Zibo system according the Zibo directory structure
 */
class Autoloader {

    private $browser;

    /**
     * Construct the autoloader
     * @param string the root path of your Zibo installation, default is autodetect
     */
    public function __construct(Browser $browser) {
        $this->browser = $browser;
    }

    /**
     * Get the file browser used by this autoloader
     * @return zibo\library\filesystem\browser\Browser
     */
    public function getBrowser() {
        return $this->browser;
    }

    /**
     * Autoload the provided class
     * @param string full class name with namespace
     * @return boolean true if succeeded, false otherwise
     */
    public function autoload($class) {
        $class = str_replace(array('\\', '_'), File::DIRECTORY_SEPARATOR, $class) . '.php';

        $file = $this->browser->getFile('test' . File::DIRECTORY_SEPARATOR . Zibo::DIRECTORY_SOURCE . File::DIRECTORY_SEPARATOR . $class);
        if ($file) {
            include_once($file->getPath());
            return true;
        }

        return false;
    }

    /**
     * Register this autoload implementation to PHP
     */
    public function registerAutoloader() {
        if (!spl_autoload_register(array($this, 'autoload'))) {
            throw new Exception('Could not register this autoloader');
        }
    }

    /**
     * Unegister this autoload implementation from PHP
     */
    public function unregisterAutoloader() {
        if (!spl_autoload_unregister(array($this, 'autoload'))) {
            throw new Exception('Could not unregister this autoloader');
        }
    }

}