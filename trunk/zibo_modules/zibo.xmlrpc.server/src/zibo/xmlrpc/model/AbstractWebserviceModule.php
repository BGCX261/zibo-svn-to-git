<?php

/**
 * @package zibo-xmlrpc-model
 */
namespace zibo\xmlrpc\model;

/**
 * Abstract implementation of the WebserviceModule
 */
abstract class AbstractWebserviceModule implements WebserviceModule {

    private $prefix;

    /**
     * Construct this webservice module
     * @param string prefix of this module's services
     */
    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * Get the prefix of this module's services
     * @return string prefix of the services
     */
    public function getPrefix() {
        return $this->prefix;
    }

}