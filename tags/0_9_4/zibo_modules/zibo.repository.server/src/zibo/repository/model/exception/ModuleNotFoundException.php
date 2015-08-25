<?php

namespace zibo\repository\model\exception;

use zibo\ZiboException;

/**
 * Exception thrown by the repository when a unexistant module is requested
 */
class ModuleNotFoundException extends ZiboException {

    /**
     * Constructs a new module not found exception
     * @param string $namespace The namespace of the requested module
     * @param string $name The name of the requested module
     * @param string $version The version of the requested module
     * @param boolean $higher Flag to see if the version is exact or could be higher. False for exact, true for higher as well
     * @return null
     */
    public function __construct($namespace, $name, $version = null, $higher = false) {
        $message = 'Module ' . $name . ' in namespace ' . $namespace . ' could not be found';

        if ($version) {
            $message .= ' for version ' . $version;
        }

        if ($higher) {
            $message .= ' or higher';
        }

        parent::__construct($message);
    }

}