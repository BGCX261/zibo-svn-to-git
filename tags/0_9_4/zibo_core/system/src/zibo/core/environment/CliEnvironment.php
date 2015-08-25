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
     * Get the query for the request
     * @return string
     */
    public function getQuery() {
        $query = $this->getArgument(0, '');
        if ($query) {
            $query = ltrim($query, Request::QUERY_SEPARATOR);
            $query = rtrim($query, Request::QUERY_SEPARATOR);
        }

        return $query;
    }

    /**
     * Get all the arguments of this environment
     * @return array
     */
    public function getArguments() {
        return $this->cli->getArguments();
    }

    /**
     * Get a argument of this environment
     * @param string $name name of the argument
     * @param mixed $default default value for when the argument is not set
     * @return mixed the value of the argument or the provided default value if the value is not set
     */
    public function getArgument($name, $default = null) {
        return $this->cli->getArgument($name, $default);
    }

}