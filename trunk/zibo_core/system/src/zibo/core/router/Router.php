<?php

namespace zibo\core\router;

/**
 * Creates a request object for the incoming request and manages the routes
 * and aliasses.
 */
interface Router {

    /**
     * Routes the request path to a Request object
     * @param string $baseUrl The base URL
     * @param string $path The requested path
     * @return zibo\core\Request
     */
    public function getRequest($baseUrl, $path);

    /**
     * Gets all the defined aliases
     * @return array Array with Alias objects
     * @see Alias
     */
    public function getAliases();

    /**
     * Gets all the defined routes
     * @return array Array with Route objects
     * @see Route
     */
    public function getRoutes();

}