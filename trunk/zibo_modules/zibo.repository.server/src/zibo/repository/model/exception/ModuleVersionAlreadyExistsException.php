<?php

namespace zibo\repository\model\exception;

use \Exception;

/**
 * Exception thrown by the repository when a new module is uploaded which already exists
 */
class ModuleVersionAlreadyExistsException extends Exception {

    /**
     * The namespace of the module
     * @var string
     */
    private $namespace;

    /**
     * The name of the module
     * @var string
     */
    private $name;

    /**
     * The version of the module
     * @var string
     */
    private $version;

    /**
     * Constructs a new exception
     * @param string $message The message for the exception
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @return null
     */
    public function __construct($message, $namespace, $name, $version) {
        parent::__construct($message);

        $this->namespace = $namespace;
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Gets the namespace of the module
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Gets the name of the module
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the version of the module
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

}