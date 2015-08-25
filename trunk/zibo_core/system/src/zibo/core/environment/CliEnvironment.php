<?php

namespace zibo\core\environment;

use zibo\core\Request;

use zibo\library\cli\Cli;

/**
 * Environment for a command line request
 */
class CliEnvironment extends Environment {

    /**
     * Name of this environment
     * @var string
     */
    const NAME = 'cli';

    /**
     * Command line library
     * @var zibo\library\cli\Cli
     */
    private $cli;

    /**
     * Construct this environment
     * @return null
     */
    public function __construct(Cli $cli) {
        $this->cli = $cli;
    }

    /**
     * Get the name of this environment
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
        return $_SERVER['SCRIPT_FILENAME'];
    }

    /**
     * Gets the requested path for the router
     * @return string
     */
    public function getRequestedPath() {
        $query = $this->cli->getArgument(0, '');
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
        return $this->cli->getArguments();
    }

    /**
     * Gets all the body parameters of this environment
     * @return array
     */
    public function getBodyArguments() {
        return array();
    }

    /**
     * Gets an instance of the CLI
     * @return zibo\library\cli\Cli
     */
    public function getCli() {
        return $this->cli;
    }

}