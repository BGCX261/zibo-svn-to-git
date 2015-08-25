<?php

namespace zibo\core\environment;

use zibo\core\Zibo;

use zibo\library\cli\Cli;

/**
 * Abstract environment for a unified way to certain attributes
 */
abstract class Environment {

    /**
     * Configuration key for the base URL of the system
     * @var string
     */
    const CONFIG_URL = 'system.url';

    /**
     * Instance of Zibo
     * @var zibo\core\Zibo
     */
    protected $zibo;

    /**
     * The base URL of the system
     * @var string
     */
    protected $baseUrl;

    /**
     * Sets the instance of Zibo
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return null
     */
    public function setZibo(Zibo $zibo) {
        $this->zibo = $zibo;
        $this->baseUrl = null;
    }

    /**
     * Gets the name of this environment
     * @return string
     */
    abstract public function getName();

    /**
     * Sets the base URL of the system
     * @param string $baseUrl The base URL
     * @return null
     */
    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Gets the base URL of the system
     * @return string
     */
    public function getBaseUrl() {
        if ($this->baseUrl) {
            return $this->baseUrl;
        }

        $baseUrl = $this->zibo->getConfigValue(self::CONFIG_URL);
        if (!$baseUrl) {
            $baseUrl = $this->generateBaseUrl();
        }

        return $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Generates the base URL of the system based on the server variables
     * @return string Base URL of the system
     */
    abstract protected function generateBaseUrl();

    /**
     * Gets the requested path for the router
     * @return string
     */
    abstract public function getRequestedPath();

    /**
     * Gets all the query parameters of this environment
     * @return array
     */
    abstract public function getQueryArguments();

    /**
     * Gets all the body parameters of this environment
     * @return array
     */
    abstract public function getBodyArguments();

    /**
     * Gets a instance of the current environment
     * @return Environment
     */
    public static function getEnvironment() {
        $cli = new Cli();

        if ($cli->isCli()) {
            return new CliEnvironment($cli);
        }

        return new WebEnvironment();
    }

}