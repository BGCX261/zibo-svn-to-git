<?php

namespace zibo\library;

use zibo\core\Zibo;

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
        if (String::isEmpty($command)) {
            throw new ZiboException('Provided command is empty');
        }

        $zibo = Zibo::getInstance();
        $zibo->runEvent(Zibo::EVENT_LOG, 'Executing command', $command);

        $output = array();
        $code = '';
        exec($command, $output, $code);
        $output = implode("\n", $output);

        if ($code == 127) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'Could not execute ' . $command, $output);
            throw new ZiboException('Could not execute ' . $command);
        }

        return $output;
    }

    /**
     * Makes a HTTP connection to see if an URL exists
     * @param string $url The URL to check
     * @param int $timeoutSeconds Seconds part of the timeout
     * @param int $timeoutMicroseconds Microseconds part of the timeout
     * @return boolean true if the URL exists and is connectable, false otherwise
     * @throws zibo\ZiboException when the provided URL is empty or not a string
     * @throws zibo\ZiboException when the provided URL could not be parsed
     */
    public static function urlExists($url, $timeoutSeconds = 3, $timeoutMicroseconds = 0) {
        if (String::isEmpty($url)) {
            throw new ZiboException('Provided URL is empty');
        }

        $exists = false;

        $urlInformation = @parse_url($url);
        if ($urlInformation === false) {
            throw new ZiboException('Provided URL is invalid');
        }

        // secured url?
        if (isset($urlInformation['scheme']) && $urlInformation['scheme'] == 'https') {
            $port = 443;
            @$socket = fsockopen('ssl://'.$urlInformation['host'], $port, $errorNumber, $errorMessage, $timeoutSeconds);
        } else {
            $port = isset($urlInformation['port']) ? $urlInformation['port'] : 80;
            @$socket = fsockopen($urlInformation['host'], $port, $errorNumber, $errorMessage, $timeoutSeconds);
        }

        if ($socket) {
            stream_set_timeout($socket, $timeoutSeconds, $timeoutMicroseconds);

            $host = $urlInformation['host'];
            if (isset($urlInformation['path'])) {
                $path = $urlInformation['path'];
            } else {
                $path = '/';
            }
            $query = '';
            if (isset($urlInformation['query'])) {
                $query = '?' . $urlInformation['query'];
            }

            $request = "HEAD " . $path . $query;
            $request .= " HTTP/1.0\r\nHost: " . $host . "\r\n\r\n";
            fputs($socket, $request);
            $response = fread($socket, 4096);
            fclose($socket);

            $exists = (bool) preg_match('#^HTTP/.*\s+[200|302]+\s#i', $response);
        }

        return $exists;
    }

}