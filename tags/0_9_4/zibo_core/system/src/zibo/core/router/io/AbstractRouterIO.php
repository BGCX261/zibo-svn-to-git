<?php

namespace zibo\core\router\io;

/**
 * Abstract implementation of the RouterIO
 */
abstract class AbstractRouterIO implements RouterIO {

    /**
     * The loaded routes
     * @var array
     */
    protected $routes;

    /**
     * Gets the defined routes of this implementation
     * @return array Array of Route instances
     */
    public function getRoutes() {
        if ($this->routes) {
            return $this->routes;
        }

        $this->routes = $this->readRoutes();

        return $this->routes;
    }

    /**
     * Get the defined route for the provided query
     * @param string full request query
     * @paramm array array with all the possible routes for the given query
     * @return zibo\core\router\Route object if found, null otherwise
     */
    public function getRouteFromQuery($query, array $routes) {
        $this->getRoutes();

        foreach ($routes as $route) {
            if (isset($this->routes[$route])) {
                return $this->routes[$route];
            }
        }

        return null;
    }

    /**
     * Reads the routes from the data source
     * @return array Array with Route instances
     */
    abstract protected function readRoutes();

}