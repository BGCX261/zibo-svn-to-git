<?php

namespace zibo\core;

/**
 * Creates a request object for the current request
 */
interface Router {

    /**
     * Route the request query to a Request object
     * @return zibo\core\Request
     */
    public function getRequest();

    /**
     * Get all the defined routes
     * @return array array with Route objects
     */
    public function getRoutes();

}