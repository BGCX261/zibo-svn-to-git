<?php

namespace zibo\admin\model\module\exception;

use zibo\library\filesystem\File;

use zibo\ZiboException;

/**
 * Exception when no module definition is found for a path
 */
class ModuleDefinitionNotFoundException extends ZiboException {

    /**
     * Constructs a new exception
     * @param zibo\library\filesystem\File $path Path of the module without a module definition
     * @return null
     */
    public function __construct(File $path) {
        parent::__construct('No module definition found in ' . $path->getPath());
    }

}

