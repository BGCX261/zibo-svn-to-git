<?php

namespace zibo\admin\model\module\exception;

use zibo\ZiboException;

/**
 * Exception thrown when looking for an unexistant module
 */
class ModuleNotFoundException extends ZiboException {

    /**
     * Constructs a new exception
     * @param string $namespace The namespace of the requested module
     * @param string $name The name of the requested module
     * @return null
     */
    public function __construct($namespace, $name) {
        parent::__construct('Module ' . $name . ' from namespace ' . $namespace . ' not found');
    }

}

