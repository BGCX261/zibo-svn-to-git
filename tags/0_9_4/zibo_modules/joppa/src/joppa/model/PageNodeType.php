<?php

namespace joppa\model;

use joppa\Module;

use zibo\library\i18n\translation\Translator;

/**
 * Implementation of the page node type
 */
class PageNodeType extends AbstractNodeType {

    /**
     * Name of the type
     * @var string
     */
    const NAME = 'page';

    /**
     * Class name of the backend controller
     * @var string
     */
    const CONTROLLER_BACKEND = 'joppa\\controller\\backend\\PageController';

    /**
     * Construct this node type
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

    /**
     * Checks if this node type is available in the frontend
     * @return boolean
     */
    public function isAvailableInFrontend() {
        return true;
    }

    /**
     * Get the class name of the backend controller
     * @return string
     */
    public function getBackendController() {
        return self::CONTROLLER_BACKEND;
    }

}