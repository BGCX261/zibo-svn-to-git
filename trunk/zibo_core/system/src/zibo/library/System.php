<?php

namespace zibo\library;

use zibo\ZiboException;

/**
 * System functions
 */
class System {

    /**
     * Checks if the server operating system is a unix variant
     * @return boolean True when the server operating system is a unix variant, false otherwise
     */
    public static function isUnix() {
        $osType = strtoupper(PHP_OS);

        switch ($osType) {
            case 'LINUX':
            case 'UNIX':
            case 'DARWIN':
                return true;
            default:
                return false;
        }
    }

    /**
     * Checks if the server operating system is Microsoft Windows
     * @return boolean True when the server operating system is Microsoft Windows, false otherwise
     */
    public static function isWindows() {
        $osType = strtoupper(PHP_OS);

        switch ($osType) {
            case 'WIN32':
            case 'WINNT':
                return true;
            default:
                return false;
        }
    }

    /**
     * Executes a command on the server
     * @param string $command
     * @return string The output of the command
     * @throws zibo\ZiboException when the provided command is empty or not a string
     * @throws zibo\ZiboException when the command could not be executed
     */
    public static function execute($command) {
        if (!String::isString($command, String::NOT_EMPTY)) {
            throw new ZiboException('Provided command is empty or not a string');
        }

        $output = array();
        $code = '';
        exec($command, $output, $code);
        $output = implode("\n", $output);

        if ($code == 127) {
            throw new ZiboException('Could not execute ' . $command);
        }

        return $output;
    }

}