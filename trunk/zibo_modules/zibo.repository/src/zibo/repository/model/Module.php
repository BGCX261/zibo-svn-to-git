<?php

namespace zibo\repository\model;

use zibo\admin\model\module\Module as AdminModule;

/**
 * Module data container
 */
class Module extends AdminModule {

    /**
     * Icon for this data object
     * @var string
     */
    const ICON = 'web/images/repository/module.png';

    /**
     * URL of the repository where this module can be found
     * @var string
     */
    private $repository = null;

    /**
     * Array with the data of the different versions
     * @var array
     */
    private $versions = array();

    /**
     * Sets the URL of the repository where this module can be found
     * @param string $url URL of the repository
     * @return null
     */
    public function setRepository($url) {
        $this->repository = $url;
    }

    /**
     * Gets the URL of the repository where this module can be found
     * @return string
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * Adds a version to this module
     * @param Module $module
     * @return null
     */
    public function addVersion(Module $module) {
        $this->versions[] = $module;
    }

    /**
     * Gets all the versions of this module
     * @return array Array with Module objects
     */
    public function getVersions() {
        return $this->versions;
    }

    /**
     * Gets the number of available versions
     * @return integer
     */
    public function countVersions() {
        return count($this->versions);
    }

}