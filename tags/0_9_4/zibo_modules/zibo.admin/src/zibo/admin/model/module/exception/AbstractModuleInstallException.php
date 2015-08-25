<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use zibo\ZiboException;

use \Exception;

/**
 * Abstract module exception thrown while installing a module
 */
abstract class AbstractModuleInstallException extends AbstractModuleException {

    /**
     * Constructs a new exception
     * @param zibo\admin\model\Module $module The module which is being installed
     * @param string $message
     * @param int $code
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, $message = null, $code = 0, Exception $previousException = null) {
        $exceptionMessage = 'Could not install ' . $module->getName() . ' from namespace ' . $module->getNamespace();
        if ($message) {
            $exceptionMessage .= ': ' . $message;
        }

        parent::__construct($module, $exceptionMessage, $code, $previousException);
    }

}