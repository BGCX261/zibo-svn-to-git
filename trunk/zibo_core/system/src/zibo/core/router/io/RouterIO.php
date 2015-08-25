<?php

namespace zibo\core\router\io;

/**
 * Interface to retrieve possible routes from a data source
 */
interface RouterIO {

    /**
     * Gets the defined aliases for this implementation
     * @return array Array with the path as key and a Alias object as value
     * @see zibo\core\router\Alias
     */
    public function getAliases();

    /**
    * Gets the defined alias for the provided path
    * @param string $path The requested path
    * @param array $routes Array with all the possible routes for the given path
    * @return zibo\core\alias\Alias|null Alias object if found, null otherwise
    */
    public function getAliasFromPath($path, array $routes);

    /**
     * Gets the defined routes for this implementation
     * @return array Array of Route objects
     * @see zibo\core\router\Route
     */
    public function getRoutes();

    /**
     * Gets the defined route for the provided path
     * @param string $path The requested path
     * @param array $routes Array with all the possible routes for the given path
     * @return zibo\core\router\Route|null Route object if found, null otherwise
     */
    public function getRouteFromPath($path, array $routes);

}