<?php

/**
 * Bootstrap of the Zibo system
 *
 * Don't edit this file. You can configure this bootstrap by creating a
 * bootstrap.config.php file. Variables should be overriden therein.
 */

/**
 * Configuration file for the bootstrap. Basicly a PHP which overrides the
 * following variables
 * @var string
 */
$configFile = 'bootstrap.config.php';

/**
 * Path to the root of this installation.
 * @var string
 */
$rootPath = '';

/**
 * Class name of the file browser.
 *
 * zibo\core\filesystem\GenericFileBrowser is set by default and recommended
 * when developing an application.
 *
 * You can use zibo\core\filesystem\IndexedFileBrowser in a production
 * environment to speed things up. If you have troubles with files not being
 * found, you can remove the application/data/cache/filebrowser.index file.
 * @var string
 */
$fileBrowserClass = 'zibo\\core\\filesystem\\GenericFileBrowser';

/**
 * Class name of the configuration I/O implementation.
 * @var string
 */
$configIOClass = 'zibo\\core\\config\\io\\ini\\IniConfigIO';

/*
 * Here we go then, let's boot. You can stop editing...
 */

// Let's see if there's a bootstrap.config.php and if so, include it
if (file_exists($configFile)) {
    include_once($configFile);
}

// make sure we have a root path set
if (!$rootPath) {
    $rootPath = getcwd();
}

// include necessairy classes
use zibo\core\environment\Environment;

use zibo\core\config\io\CachedConfigIO;
use zibo\core\Autoloader;
use zibo\core\ErrorHandler;

use zibo\library\filesystem\File;

require_once $rootPath . '/system/src/zibo/ZiboException.php';
require_once $rootPath . '/system/src/zibo/library/String.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/exception/FileSystemException.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/File.php';
require_once $rootPath . '/system/src/zibo/library/filesystem/FileSystem.php';
require_once $rootPath . '/system/src/zibo/core/Autoloader.php';
require_once $rootPath . '/system/src/zibo/core/ErrorHandler.php';
require_once $rootPath . '/system/src/zibo/core/Zibo.php';
require_once $rootPath . '/system/src/zibo/core/filesystem/FileBrowser.php';
require_once $rootPath . '/system/src/zibo/core/filesystem/AbstractFileBrowser.php';
require_once $rootPath . '/system/src/' . str_replace('\\', '/', $fileBrowserClass) . '.php';

// register the error handler
$errorHandler = new ErrorHandler();
$errorHandler->registerErrorHandler();

// initialize the file browser
$rootPath = new File($rootPath);
$fileBrowser = new $fileBrowserClass($rootPath);

// register the autoloader
$autoloader = new Autoloader($fileBrowser);
$autoloader->registerAutoloader();

// get the environment
$environment = Environment::getEnvironment();

// initialize the configuration i/o
$configIO = new $configIOClass($environment, $fileBrowser);
$configIO = new CachedConfigIO($configIO, $environment);