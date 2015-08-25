<?php

namespace zibo\core;

use zibo\library\filesystem\browser\Browser;
use zibo\library\filesystem\File;

use \Exception;

/**
 * Autoloader for the Zibo system according the Zibo directory structure
 */
class Autoloader {

    /**
     * A file browser to lookup files
     * @var zibo\library\filesystem\browser\Browser
     */
    private $browser;

    /**
     * Construct a new autoloader
     * @param zibo\library\filesystem\browser\Browser $browser a file browser to lookup files
     * @return null
     */
    public function __construct(Browser $browser) {
        $this->browser = $browser;
    }

    /**
     * Gets the file browser used by this autoloader
     * @return zibo\library\filesystem\browser\Browser
     */
    public function getBrowser() {
        return $this->browser;
    }

    /**
     * Autoloads the provided class
     * @param string $class full class name with namespace
     * @return boolean true if succeeded, false otherwise
     */
    public function autoload($class) {
        $class = str_replace(array('\\', '_'), File::DIRECTORY_SEPARATOR, $class) . '.php';

        $file = $this->browser->getFile(Zibo::DIRECTORY_SOURCE . File::DIRECTORY_SEPARATOR . $class);
        if ($file) {
            include_once($file->getPath());
            return true;
        }

        $file = $this->browser->getFile(Zibo::DIRECTORY_VENDOR . File::DIRECTORY_SEPARATOR . $class);
        if ($file) {
            include_once($file->getPath());
            return true;
        }

        $directories = explode(PATH_SEPARATOR, get_include_path());
        foreach ($directories as $directory) {
            $file = realpath($directory) . DIRECTORY_SEPARATOR . $class;
            if (file_exists($file)) {
                include_once($file);
                return true;
            }
        }

        return false;
    }

    /**
     * Registers this autoload implementation to PHP
     * @return null
     */
    public function registerAutoloader() {
        if (!spl_autoload_register(array($this, 'autoload'))) {
            throw new Exception('Could not register this autoloader');
        }
    }

    /**
     * Unegisters this autoload implementation from PHP
     * @return null
     */
    public function unregisterAutoloader() {
        if (!spl_autoload_unregister(array($this, 'autoload'))) {
            throw new Exception('Could not unregister this autoloader');
        }
    }

}