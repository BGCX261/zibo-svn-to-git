<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use zibo\ZiboException;

use \Exception;

/**
 * Exception thrown when a dependency is not installed
 */
class ModuleDependencyNotInstalledException extends AbstractModuleDependencyInstallException {

    /**
     * Constructs a new exception
     * @param zibo\admin\model\Module $module The module which is being installed
     * @param zibo\admin\model\Module $dependency The dependency which is not installed
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, Module $dependency, Exception $previousException = null) {
        $message = 'Dependency ' . $dependency->getName() . ' from namespace ' . $dependency->getNamespace() . ' is not installed';

        parent::__construct($module, $dependency, $message, 0, $previousException);
    }

}

