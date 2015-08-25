<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use \Exception;

/**
 * Exception thrown when a dependency has not the required version
 */
class ModuleDependencyVersionNotInstalledException extends AbstractModuleDependencyInstallException {

    /**
     * The installed version of the dependency
     * @param string
     */
    private $installedVersion;

    /**
     * Constructs a new exception
     * @param zibo\admin\model\Module $module The module which is being installed
     * @param zibo\admin\model\Module $dependency The dependency which is not met
     * @param string $installedVersion The currently installed version
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, Module $dependency, $installedVersion, Exception $previousException = null) {
        $this->installedVersion = $installedVersion;

        $message = 'Dependency ' . $dependency->getName() . ' from namespace ' . $dependency->getNamespace() . ' is not met. Version ' . $dependency->getVersion() . ' is needed, got ' . $installedVersion;

        parent::__construct($module, $dependency, $message, 0, $previousException);
    }

    /**
     * Gets the installed version of the dependency
     * @return string
     */
    public function getInstalledVersion() {
        return $this->installedVersion;
    }

}