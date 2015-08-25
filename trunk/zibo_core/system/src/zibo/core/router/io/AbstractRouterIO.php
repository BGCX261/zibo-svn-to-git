<?php

namespace zibo\core\router\io;

/**
 * Abstract implementation of the RouterIO
 */
abstract class AbstractRouterIO implements RouterIO {

    /**
     * The loaded aliases
     * @var array
     */
    protected $aliases;

    /**
     * The loaded routes
     * @var array
     */
    protected $routes;

    /**
    * Gets the defined aliases for this implementation
    * @return array Array with the path as key and a Alias object as value
    */
    public function getAliases() {
    	if ($this->aliases) {
    		return $this->aliases;
    	}

    	$this->aliases = $this->readAliases();

    	return $this->aliases;
    }

    /**
     * Gets the defined alias for the provided query
     * @param string $path The requested path
     * @paramm array $routes Array with all the possible routes for the given path
     * @return zibo\core\router\Alias object if found, null otherwise
     */
    public function getAliasFromPath($path, array $routes) {
    	$this->getAliases();

    	foreach ($routes as $route) {
    		if (isset($this->aliases[$route])) {
    			return $this->aliases[$route];
    		}
    	}

    	return null;
    }

    /**
     * Reads the aliases from the data source
     * @return array Array with the path of the alias as key and the destination path as value
     */
    abstract protected function readAliases();

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
     * @param string $path The requested path
     * @paramm array $routes All the possible routes for the given path
     * @return zibo\core\router\Route object if found, null otherwise
     */
    public function getRouteFromPath($path, array $routes) {
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