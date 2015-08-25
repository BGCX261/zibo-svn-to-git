<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use zibo\ZiboException;

use \Exception;

/**
 * Abstract module exception thrown while installing a module with a unmet dependency
 */
abstract class AbstractModuleDependencyInstallException extends AbstractModuleInstallException {

    /**
     * The unmet dependency
     * @var zibo\admin\model\Module
     */
    private $dependency;

    /**
     * Constructs a new module exception
     * @param zibo\admin\model\Module $module The module which is being installed
     * @param zibo\admin\model\Module $dependency The dependency which is not met
     * @param string $message
     * @param int $code
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, Module $dependency, $message = null, $code = 0, Exception $previousException = null) {
        $this->dependency = $dependency;

        parent::__construct($module, $message, $code, $previousException);
    }

    /**
     * Gets the dependency which is unmet
     * @return zibo\admin\model\Module
     */
    public function getDependency() {
        return $this->dependency;
    }

}