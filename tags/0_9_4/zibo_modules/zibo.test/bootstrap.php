<?php

use zibo\core\Autoloader;
use zibo\core\ErrorHandler;

use zibo\test\Autoloader as TestAutoloader;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

/**
 * Bootstrap of the Zibo system
 */

$ziboDir = getcwd();

require_once "$ziboDir/system/src/zibo/ZiboException.php";
require_once "$ziboDir/system/src/zibo/core/Autoloader.php";
require_once "$ziboDir/system/src/zibo/core/Zibo.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/browser/Browser.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/browser/AbstractBrowser.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/browser/GenericBrowser.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/exception/FileSystemException.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/FileSystem.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/File.php";
require_once "$ziboDir/system/src/zibo/library/filesystem/UnixFileSystem.php";
require_once "$ziboDir/system/src/zibo/library/String.php";

require_once __DIR__ . '/src/zibo/test/Autoloader.php';

$browser = new GenericBrowser(new File($ziboDir));

$autoloader = new Autoloader($browser);
$autoloader->registerAutoloader();

$errorHandler = new ErrorHandler();
$errorHandler->registerErrorHandler();

$testAutoloader = new TestAutoloader($browser);
$testAutoloader->registerAutoloader();