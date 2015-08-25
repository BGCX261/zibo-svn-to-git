<?php

/**
 * Main script of the Zibo system
 *
 * Check Bootstrap.php to modify the base configuration
 */

use zibo\core\Zibo;

use \Exception;

try {
    // include the bootstrap to initialize error handling, autoloader and
    // dependencies. it should also create $fileBrowser, $configIO and $environment
    require_once('bootstrap.php');

    // create an instance of Zibo
    $zibo = new Zibo($fileBrowser, $configIO);
    $zibo->setEnvironment($environment);
    $zibo->setDefaultTimeZone('Europe/Brussels');
    $zibo->setDefaultLocale(array('en_IE.utf8', 'en_IE', 'en'));

    // run Zibo
    $zibo->bootModules();
    $zibo->main();
} catch (Exception $exception) {
    // uncaught exception
    $class = get_class($exception);
    $message = $exception->getMessage();
    $trace = $exception->getTraceAsString();

    // try to log the exception
    try {
        if (isset($zibo)) {
            $zibo->triggerEvent(Zibo::EVENT_LOG, $class . ($message ? ': ' . $message : ''), $trace);
        }
    } catch (Exception $e) {

    }

    $title = 'Uncaught exception (' . $class . ')' . ($message ? ': ' . $message : '');

    if (php_sapi_name() == 'cli') {
        echo "\n" . $title . "\n\n" . $trace;
        exit;
    }

    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
    header($protocol . ' 500');

    echo '<div style="background-color: #fdf5d9; padding: 14px; border: 1px solid #fceec1; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; color: #404040; text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);">';
    echo '<strong>' . $title . '</strong>';
    echo '<pre>' . $trace  . '</pre>';
    echo '</div>';
}