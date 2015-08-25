<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use zibo\core\Zibo;

use \Exception;

/**
 * Exception thrown when the required Zibo is not installed
 */
class ModuleZiboVersionNotInstalledException extends AbstractModuleInstallException {

    /**
     * Constructs a new exception
     * @param zibo\admin\model\Module $module Module which is being installed
     * @param string $ziboVersion The required Zibo version
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, $ziboVersion, Exception $previousException = null) {
        $message = 'Zibo version ' . $ziboVersion . ' is needed, got version ' . Zibo::VERSION;

        parent::__construct($module, $message, 0, $previousException);
    }

}

