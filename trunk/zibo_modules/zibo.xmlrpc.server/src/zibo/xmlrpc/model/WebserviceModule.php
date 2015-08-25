<?php

/**
 * @package zibo-xmlrpc-model
 */
namespace zibo\xmlrpc\model;

/**
 * Interface of a webservice module
 */
interface WebserviceModule {

    /**
     * Get the prefix of this module's services
     * @return string module prefix
     */
    public function getPrefix();

    /**
     * Get a definition of this module's services
     * @return array contained with definition arrays. A definition array define's a service:
     *      element 0 => name of the method without the server or module prefix,
     *      element 1 => callback to the function,
     *      element 2 => return type of the method,
     *      element 3 => parameter type(s) of the method (null when no parameters, scalar variable for 1 parameter and an array for multiple parameters),
     *      element 4 => description of the service
     */
    public function getServices();

}