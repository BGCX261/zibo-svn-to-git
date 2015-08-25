<?php

/**
 * Bootstrap of the Zibo system
 */

/*
 * Configuration of the bootstrap. Use a bootstrap.config.php file to override the variables if needed.
 */

/**
 * Configuration file for the bootstrap. Basicly a PHP which overrides the following variables
 * @var string
 */
$configFile = 'bootstrap.config.php';

/**
 * Path to the root of this installation.
 * @var string
 */
$rootPath = getcwd();

/**
 * Class name of the file browser.
 *
 * zibo\library\filesystem\browser\GenericBrowser is set by default and recommended
 * when developing an application.
 *
 * You can use zibo\library\filesystem\browser\IndexedBrowser in a production
 * environment to speed things up. If you have troubles with files not being found,
 * you can remove the application/data/cache/filebrowser.index file.
 * @var string
 */
$fileBrowserClass = 'zibo\\library\\filesystem\\browser\\GenericBrowser';

/**
 * Class name of the configuration I/O implementation.
 * @var string
 */
$configIOClass = 'zibo\\library\\config\\io\\ini\\IniConfigIO';

/**
 * Let's see if there's a bootstrap.config.php and if so, include it
 */
if (file_exists($configFile)) {
    include_once($configFile);
}

/*
 * Here we go then, let's boot. You can stop editing...
 */

use zibo\core\environment\Environment;

use zibo\core\Autoloader;
use zibo\core\ErrorHandler;

use zibo\library\config\io\CachedConfigIO;

use zibo\library\filesystem\File;

require_once $rootPath . '/system/src/zibo/ZiboException.php';
require_once $rootPath . '/system/src/zibo/core/Autoloader.php';
require_once $rootPath . '/system/src/zibo/core/ErrorHandler.php';
require_once $rootPath . '/system/src/zibo/core/Zibo.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/browser/Browser.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/browser/AbstractBrowser.php';
require_once $rootPath . '/system/src/' . str_replace('\\', '/', $fileBrowserClass) . '.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/exception/FileSystemException.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/File.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/FileSystem.php';
require_once $rootPath . '/system/src/zibo/library/String.php';

$errorHandler = new ErrorHandler();
$errorHandler->registerErrorHandler();

$rootPath = new File($rootPath);
$fileBrowser = new $fileBrowserClass($rootPath);

$autoloader = new Autoloader($fileBrowser);
$autoloader->registerAutoloader();

$environment = Environment::getInstance();

$innerConfigIO = new $configIOClass($environment, $fileBrowser);

$configIO = new CachedConfigIO($environment, $innerConfigIO);

unset($fileBrowserClass);
unset($configIOClass);