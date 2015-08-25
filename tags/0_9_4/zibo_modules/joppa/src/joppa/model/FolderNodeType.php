<?php

namespace joppa\model;

use joppa\Module;

/**
 * Implementation of the folder node type
 */
class FolderNodeType extends AbstractNodeType {

    /**
     * Name of the type
     * @var string
     */
    const NAME = 'folder';

    /**
     * Class name of the backend controller
     * @var string
     */
    const CONTROLLER_BACKEND = 'joppa\\controller\\backend\\FolderController';


    /**
     * Construct this node type
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

    /**
     * Gets the default inherit value for a new node setting
     * @return boolean
     */
    public function getDefaultInherit() {
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