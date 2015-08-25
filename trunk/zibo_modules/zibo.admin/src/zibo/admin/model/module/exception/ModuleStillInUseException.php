<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use \Exception;

/**
 * Exception thrown by the module model when a module is being removes which is still needed
 * by another module.
 */
class ModuleStillInUseException extends AbstractModuleException {

    /**
     * Constructs a new exception
     * @param zibo\admin\model\Module $module The module which is still needed by other modules
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, Exception $previousException = null) {
        $usage = $module->getUsage();

        $usageString = '';
        foreach ($usage as $usedModule) {
            $usageString .= ($usageString ? ', ' : '') . $usedModule->getName() . ' from namespace ' . $usedModule->getNamespace();
        }
        $message = 'Could not remove ' . $module->getName() . ' from namespace ' . $module->getNamespace() . ': still used by ';

        parent::__construct($module, $message . $usageString, 0, $previousException);
    }

}

