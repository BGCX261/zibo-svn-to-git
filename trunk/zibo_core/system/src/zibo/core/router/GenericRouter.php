<?php

namespace zibo\core\router;

use zibo\core\environment\Environment;
use zibo\core\router\io\RouterIO;
use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\ObjectFactory;
use zibo\library\String;
use zibo\library\Url;

use zibo\ZiboException;

/**
 * Generic router implementation
 */
class GenericRouter implements Router {

    /**
     * Instance of the environment
     * @var zibo\core\environment\Environment
     */
    private $environment;

    /**
     * Instance of the route I/O implementation
     * @var zibo\core\router\io\RouterIO
     */
    private $io;

    /**
     * Full class name of the default controller
     * @var string
     */
    private $defaultController;

    /**
     * Name of the default action method
     * @var string
     */
    private $defaultAction;

    /**
     * Construct a new router
     * @param zibo\core\router\io\RouterIO $io Route I/O implementation to use
     * @return null
     */
    public function __construct(Environment $environment, RouterIO $io) {
        $this->environment = $environment;
        $this->io = $io;

        $defaultController = null;
        $defaultAction = null;
    }

    /**
     * Sets the default action of this router
     * @param string $defaultController full class name of the default controller
     * @param string $defaultAction method name of the default action in the controller
     * @return null
     * @throws zibo\ZiboException when the default controller is an invalid or empty value
     * @throws zibo\ZiboException when the default action is an invalid or empty value
     */
    public function setDefaultAction($defaultController, $defaultAction = null) {
        if (!String::isString($defaultController, String::NOT_EMPTY)) {
            throw new ZiboException('Provided default controller is empty or not a string');
        }

        if ($defaultAction !== null && !String::isString($defaultAction, String::NOT_EMPTY)) {
            throw new ZiboException('Provided default action is empty or not a string');
        }

        $this->defaultController = $defaultController;
        $this->defaultAction = $defaultAction;
    }

    /**
     * Gets the default controller
     * @return string full class name of the default controller
     */
    public function getDefaultController() {
        return $this->defaultController;
    }

    /**
     * Gets the default action
     * @return string method name of the default action
     */
    public function getDefaultAction() {
        return $this->defaultAction;
    }

    /**
     * Gets all the defined aliases
     * @return array array with Alias objects
     * @see zibo\core\router\Alias
     */
    public function getAliases() {
        return $this->io->getAliases();
    }

    /**
     * Gets all the defined routes
     * @return array array with Route objects
     * @see zibo\core\router\Route
     */
    public function getRoutes() {
        return $this->io->getRoutes();
    }

    /**
     * Route the request query to a Request object
     * @param string $baseUrl The base URL
     * @param string $path The requested path
     * @return zibo\core\Request
     */
    public function getRequest($baseUrl, $path) {
        $routes = $this->getRoutesFromPath($path);

        $request = $this->getRouteRequest($baseUrl, $path, $routes);
        if ($request) {
        	return $request;
        }

        $request = $this->getAliasRequest($baseUrl, $path, $routes);
        if ($request) {
        	return $request;
        }

        if ($defaultController = $this->getDefaultController()) {
            $parameters = $this->getParametersFromPath($path, '');

            return $this->createRequest($baseUrl, $baseUrl, $defaultController, $this->getDefaultAction(), $parameters);
        }

        return null;
    }

    /**
     * Gets a request from the route definitions for the requested path
     * @param string $baseUrl The base URL
     * @param string $path The requested path
     * @param array $routes The possible routes for the path
     * @return zibo\core\Request|null A request object if a route was found,
     * null otherwise
     */
    protected function getRouteRequest($baseUrl, $path, array $routes) {
        $route = $this->io->getRouteFromPath($path, $routes);

        if ($route === null) {
        	return null;
        }

        $basePath = $baseUrl . Request::QUERY_SEPARATOR . $route->getPath();
        $controller = $route->getControllerClass();
        $action = $route->getAction();
        $parameters = $this->getParametersFromPath($path, $route->getPath());

        return $this->createRequest($baseUrl, $basePath, $controller, $action, $parameters);
    }

    /**
     * Gets a request from the alias definitions for the requested path
     * @param string $baseUrl The base URL
     * @param string $path The requested path
     * @param array $routes The possible routes for the path
     * @return zibo\core\Request|null A request object if a route was found,
     * null otherwise
     */
    protected function getAliasRequest($baseUrl, $path, array $routes) {
    	$alias = $this->io->getAliasFromPath($path, $routes);

    	if ($alias === null) {
    		return null;
    	}

    	$path = str_replace($alias->getPath(), $alias->getDestination(), $path);
    	$routes = $this->getRoutesFromPath($path);

    	$request = $this->getRouteRequest($baseUrl, $path, $routes);
    	if ($request === null) {
    		return null;
    	}

    	$basePath = $baseUrl . Request::QUERY_SEPARATOR . $alias->getPath();
    	$parameters = $request->getParameters();

    	return $this->createRequest($baseUrl, $basePath, $request->getControllerName(), $request->getActionName(), $parameters);
    }

    /**
     * Creates a request object with the provided parameters and adds the
     * query parameters and the body parameters to it
     * @param string $baseUrl The base URL
     * @param string $basePath The base URL with the action path concatted
     * @param string $controller The full class name of the controller
     * @param string|null $action The name of the action method
     * @param array $parameters Array with the parameters for the action method
     * @return zibo\core\Request The created request
     */
    final protected function createRequest($baseUrl, $basePath, $controller, $action, $parameters) {
        $queryParameters = $this->environment->getQueryArguments();
        $bodyParameters = $this->environment->getBodyArguments();
        return new Request($baseUrl, $basePath, $controller, $action, $parameters, $queryParameters, $bodyParameters);
    }

    /**
     * Gets all the possible routes from the query
     * @param string $query the full query
     * @return array Array with all the possible routes
     */
    final protected function getRoutesFromPath($path) {
        $routes = array();

        $route = '';
        $tokens = explode(Request::QUERY_SEPARATOR, $path);
        foreach ($tokens as $token) {
            $route .= $token;
            $routes[] = $route;
            $route .= Request::QUERY_SEPARATOR;
        }

        $routes = array_reverse($routes);

        return $routes;
    }

    /**
     * Gets the action parameters from the path
     * @param string $path the requested path
     * @param string $route the matched route to a controller
     * @return array An array with all the path tokens which are not in the route
     */
    final protected function getParametersFromPath($path, $route) {
        $path = substr_replace($path, '', 0, strlen($route));
        $path = ltrim($path, Request::QUERY_SEPARATOR);

        if (empty($path)) {
            return array();
        }

        $parameters = explode(Request::QUERY_SEPARATOR, $path);
        foreach ($parameters as $key => $value) {
            $parameters[$key] = urldecode($value);
        }

        return $parameters;
    }

}