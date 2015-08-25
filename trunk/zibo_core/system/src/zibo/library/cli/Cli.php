<?php

namespace zibo\library\cli;

/**
 * Command line library
 */
class Cli {

    /**
     * Value of the SAPI for the command line interface
     * @var string
     */
    const SAPI_CLI = 'cli';

    /**
     * Flag to set whether we are running the command line interface
     * @var boolean
     */
    private $isCli;

    /**
     * Array with the arguments of the command line
     * @var array
     */
    private $arguments;

    /**
     * Construct this command line library
     * @param null|array $arguments
     */
    public function __construct(array $arguments = null) {
        $this->isCli = php_sapi_name() == self::SAPI_CLI;

        if (!$this->isCli) {
            $this->arguments = array();
            return;
        }

        if ($arguments === null) {
            $arguments = $_SERVER['argv'];
            array_shift($arguments); // remove first element, always the script name
        }

        $this->arguments = ArgumentParser::parseArguments($arguments);
    }

    /**
     * Check whether we are running the command line interface
     * @return bool true if we are running the command line interface, false
     * otherwise
     */
    public function isCli() {
        return $this->isCli;
    }

    /**
     * Get all the arguments of the command line
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Get an argument of the command line
     * @param string $name name of the argument
     * @param mixed $default default value for when the argument is not set
     * @return mixed the value of the argument or the provided default value
     * when the argument is not set
     */
    public function getArgument($name, $default = null) {
        if (!isset($this->arguments[$name])) {
            return $default;
        }

        return $this->arguments[$name];
    }

}