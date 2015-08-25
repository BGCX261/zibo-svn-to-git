<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use \Exception;

/**
 * Exception thrown when a module is being installed with no required Zibo version set.
 */
class ModuleZiboVersionNeededException extends AbstractModuleInstallException {

    /**
     * Constructs a new exception
     * @param zibo\admin\model\Module $module The module which needs a Zibo version
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, Exception $previousException = null) {
        $message = 'Module has no required Zibo version defined.';

        parent::__construct($module, $message, 0, $previousException);
    }

}

