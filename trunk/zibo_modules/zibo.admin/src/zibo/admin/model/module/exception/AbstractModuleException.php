<?php

namespace zibo\admin\model\module\exception;

use zibo\admin\model\module\Module;

use zibo\ZiboException;

use \Exception;

/**
 * Abstract module exception
 */
abstract class AbstractModuleException extends ZiboException {

    /**
     * Module of this exception
     * @var zibo\admin\model\Module
     */
    protected $module;

    /**
     * Constructs a new module exception
     * @param zibo\admin\model\Module $module
     * @param string $message
     * @param int $code
     * @param Exception $previousException
     * @return null
     */
    public function __construct(Module $module, $message = null, $code = 0, Exception $previousException = null) {
        $this->module = $module;
        parent::__construct($message, $code, $previousException);
    }

    /**
     * Gets the module of this exception
     * @return zibo\admin\model\Module
     */
    public function getModule() {
        return $this->module;
    }

}