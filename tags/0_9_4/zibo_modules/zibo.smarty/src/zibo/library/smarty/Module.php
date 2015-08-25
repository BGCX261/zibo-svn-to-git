<?php

namespace zibo\library\smarty;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\smarty\view\SmartyView;

/**
 * Initializer for the Smarty module
 */
class Module {

    /**
     * Initialize the Smarty module for a request
     * @return null
     */
    public function initialize() {
        Zibo::getInstance()->registerEventListener(Zibo::EVENT_CLEAR_CACHE, array($this, 'clearCache'));
    }

    /**
     * Clear the Smarty cache
     * @return null
     */
    public function clearCache() {
        $compileDirectory = Zibo::getInstance()->getConfigValue(SmartyView::CONFIG_COMPILE_DIRECTORY, SmartyView::DEFAULT_COMPILE_DIRECTORY);
        $compileDirectory = new File($compileDirectory);
        $compileDirectory->delete();
    }

}