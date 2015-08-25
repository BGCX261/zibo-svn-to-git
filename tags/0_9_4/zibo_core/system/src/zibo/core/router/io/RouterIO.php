<?php

namespace zibo\core\router\io;

/**
 * Interface to retrieve possible routes from a data source
 */
interface RouterIO {

    /**
     * Gets the defined routes for this implementation
     * @return array array of Route objects
     */
    public function getRoutes();

    /**
     * Gets the defined route for the provided query
     * @param string full request query
     * @paramm array array with all the possible routes for the given query
     * @return Route object if found, null otherwise
     */
    public function getRouteFromQuery($query, array $routes);

}