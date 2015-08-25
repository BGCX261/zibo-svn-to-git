<?php

/**
 * Main script of the Zibo system
 *
 * Check Bootstrap.php to modify the base configuration
 */

use zibo\core\Zibo;

use \Exception;

require_once('bootstrap.php');

try {
    // the bootstrap should have initiated the $fileBrowser, $configIO and $environment
    $zibo = Zibo::getInstance($fileBrowser, $configIO);
    $zibo->setEnvironment($environment);

    // initialize locale and timezone
    date_default_timezone_set($zibo->getConfigValue(Zibo::CONFIG_TIMEZONE, 'Europe/Brussels'));
    setlocale(LC_ALL, $zibo->getConfigValue(Zibo::CONFIG_LOCALE, array('en_IE.utf8', 'en_IE', 'en')));

    // run zibo
    $zibo->run();
    exit;
} catch (Exception $exception) {
    // uncaught exception

    $class = get_class($exception);
    $message = $exception->getMessage();
    $trace = $exception->getTraceAsString();

    // log the exception
    try {
        $zibo->runEvent(Zibo::EVENT_LOG, $class . ($message ? ': ' . $message : ''), $trace);
    } catch (Exception $e) {

    }

    $title = 'Uncaught exception (' . $class . ')' . ($message ? ': ' . $message : '');

    if (php_sapi_name() == 'cli') {
        echo "\n" . $title . "\n\n" . $trace;
        exit;
    }

    echo '<div style="margin: 20px; padding: 20px; border: 1px solid #cf2e2e; background-color: #F6D5CB; color: #cf2e2e;">';
    echo '<strong>' . $title . '</strong>';
    echo '<pre>' . $trace  . '</pre>';
    echo '</div>';
}