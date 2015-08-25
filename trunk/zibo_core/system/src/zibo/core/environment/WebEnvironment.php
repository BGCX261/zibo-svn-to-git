<?php

namespace zibo\core\environment;

use zibo\core\Request;

/**
 * Environment for a web request
 */
class WebEnvironment extends Environment {

    /**
     * Name of this environment
     * @var string
     */
    const NAME = 'web';

    /**
     * Name of the route query argument
     * @var string
     */
    const QUERY_NAME = 'q';

    /**
     * Gets the name of this environment
     * @return string
     */
    public function getName() {
        return self::NAME;
    }

    /**
    * Generates the base URL of the system
    * @return string
    */
    protected function generateBaseUrl() {
        if ($this->baseUrl) {
            return $this->baseUrl;
        }

        $port = $_SERVER['SERVER_PORT'];

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url = 'https://' . $_SERVER['SERVER_NAME'];
            if (!empty($port) && $port != 443) {
                $url .=  ':' . $port;
            }
        } else {
            $url = 'http://' . $_SERVER['SERVER_NAME'];
            if (!empty($port) && $port != 80) {
                $url .=  ':' . $port;
            }
        }

        $script = $_SERVER['SCRIPT_NAME'];
        if (strpos($script, '/') === false) {
            $script = '/' . $script;
        }
        $url .= $script;

        return $this->baseUrl = dirname($url);
    }

    /**
     * Gets the requested path for the router
     * @return string
     */
    public function getRequestedPath() {
        $query = '';

        if (isset($_GET[self::QUERY_NAME])) {
            $query = $_GET[self::QUERY_NAME];
        }

        if ($query) {
            $query = ltrim($query, Request::QUERY_SEPARATOR);
            $query = rtrim($query, Request::QUERY_SEPARATOR);
        }

        return $query;
    }

    /**
     * Gets all the query parameters of this environment
     * @return array
     */
    public function getQueryArguments() {
        $arguments = $_GET;
        if (isset($arguments[self::QUERY_NAME])) {
            unset($arguments[self::QUERY_NAME]);
        }

        return $this->decodeArguments($arguments);
    }

    /**
     * Gets all the body parameters of this environment
     * @return array
     */
    public function getBodyArguments() {
        return $this->decodeArguments($_POST);
    }

    /**
     * URL decodes the provided arguments
     * @param array $arguments The arguments to decode
     * @return array Array with provided arguments decoded
     */
    private function decodeArguments(array $arguments) {
        $decodedArguments = array();

        foreach ($arguments as $key => $value) {
            $decodedArguments[$key] = urldecode($value);
        }

        return $decodedArguments;
    }

}