<?php

namespace zibo\core\environment;

use zibo\library\cli\Cli;
use zibo\library\Boolean;

/**
 * Abstract environment for a unified way to certain attributes
 */
abstract class Environment {

    /**
     * The instance of the Environment object
     * @var zibo\core\environment\Environment
     */
    private static $instance;

    /**
     * Get the instance of the environment
     * @return Environment
     */
    public static function getInstance() {
        if (self::$instance == null) {
            $cli = new Cli();
            if ($cli->isCli()) {
                self::$instance = new CliEnvironment($cli);
            } else {
                self::$instance = new WebEnvironment();
            }
        }

        return self::$instance;
    }

    /**
     * Get the name of this environment
     * @return string
     */
    abstract public function getName();

    /**
     * Get the query for the request
     * @return string
     */
    abstract public function getQuery();

    /**
     * Get all the arguments of this environment
     * @return string
     */
    abstract public function getArguments();

    /**
     * Get a argument of this environment
     * @param string $name name of the argument
     * @param mixed $default default value for when the argument is not set
     * @return mixed the value of the argument or the provided default value if the value is not set
     */
    abstract public function getArgument($name, $default = null);

    /**
     * Get a boolean argument of this environment
     * @param string $name name of the boolean argument
     * @param boolean $default default value for when the argument is not set
     * @return boolean the value of the argument or the provided default value if the value is not set
     * @see zibo\library\Boolean::getBoolean()
     */
    public function getBooleanArgument($name, $default = null) {
        $value = $this->getArgument($name, $default);
        return Boolean::getBoolean($value);
    }

}