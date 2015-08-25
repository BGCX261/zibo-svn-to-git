<?php

namespace zibo\core\router;

use zibo\core\router\io\RouterIO;
use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Router;
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
     * Configuration key for the implementation of the route I/O
     * @var string
     */
    const CONFIG_ROUTER_IO = 'system.router.io';

    /**
     * Full class name of the route I/O interface
     * @var string
     */
    const CLASS_ROUTER_IO = 'zibo\\core\\router\\io\\RouterIO';

    /**
     * Full class name of the default route I/O implementation
     * @var string
     */
    const DEFAULT_ROUTER_IO = 'zibo\\core\\router\\io\\XmlRouterIO';

    /**
     * Instance of the route I/O implementation
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
     * @param zibo\core\router\io\RouterIO $io route I/O implementation to use
     * @return null
     */
    public function __construct(RouterIO $io = null) {
        $this->io = $io;
    }

    /**
     * Set the default action of this router
     * @param string $defaultController full class name of the default controller
     * @param string $defaultAction method name of the default action in the controller
     * @return null
     * @throws zibo\ZiboException when the default controller is an invalid or empty value
     */
    public function setDefaultAction($defaultController, $defaultAction = null) {
        if (String::isEmpty($defaultController)) {
            throw new ZiboException('Provided default controller is empty');
        }
        if (String::isEmpty($defaultAction)) {
            $defaultAction = Dispatcher::ACTION_ASTERIX;
        }

        $this->defaultController = $defaultController;
        $this->defaultAction = $defaultAction;
    }

    /**
     * Get the default controller
     * @return string full class name of the default controller
     */
    public function getDefaultController() {
        return $this->defaultController;
    }

    /**
     * Get the default action
     * @return string method name of the default action
     */
    public function getDefaultAction() {
        return $this->defaultAction;
    }

    /**
     * Get all the defined routes
     * @return array array with Route objects
     */
    public function getRoutes() {
        $io = $this->getIO();
        return $io->getRoutes();
    }

    /**
     * Route the request query to a Request object
     * @return zibo\core\Request
     */
    public function getRequest() {
        $query = $this->getQuery();
        $routes = $this->getRoutesFromQuery($query);

        $io = $this->getIO();
        $route = $io->getRouteFromQuery($query, $routes);

        $request = null;
        $baseUrl = Url::getSystemBaseUrl();
        if ($route !== null) {
            $basePath = $baseUrl . Request::QUERY_SEPARATOR . $route->getPath();
            $controller = $route->getControllerClass();
            $action = $route->getAction();
            $parameters = $this->getParametersFromQuery($query, $route->getPath());

            $request = new Request($baseUrl, $basePath, $controller, $action, $parameters);
        } elseif ($defaultController = $this->getDefaultController()) {
            $parameters = $this->getParametersFromQuery($query, '');

            $request = new Request($baseUrl, $baseUrl, $defaultController, $this->getDefaultAction(), $parameters);
        }

        return $request;
    }

    /**
     * Get the full query of the request
     * @return string
     */
    protected function getQuery() {
        $environment = Zibo::getInstance()->getEnvironment();
        return $environment->getQuery();
    }

    /**
     * Get all the possible routes from the query
     * @param string $query the full query
     * @return array Array with all the possible routes
     */
    final protected function getRoutesFromQuery($query) {
        $routes = array();

        $route = '';
        $tokens = explode(Request::QUERY_SEPARATOR, $query);
        foreach ($tokens as $token) {
            $route .= $token;
            $routes[] = $route;
            $route .= Request::QUERY_SEPARATOR;
        }

        $routes = array_reverse($routes);

        return $routes;
    }

    /**
     * Get the parameters from the query
     * @param string $query the full query
     * @param string $route the matched route to a controller
     * @return array An array with all the query tokens which are not in the route
     */
    final protected function getParametersFromQuery($query, $route) {
        $query = substr_replace($query, '', 0, strlen($route));
        $query = ltrim($query, Request::QUERY_SEPARATOR);
        if (empty($query)) {
            return array();
        }
        return explode(Request::QUERY_SEPARATOR, $query);
    }

    /**
     * Get the route I/O implementation. When no implementation is provided through the constructor,
     * a new implementation will be constructed based on the configuration.
     * @return zibo\core\router\io\RouterIO
     */
    final protected function getIO() {
        if ($this->io) {
            return $this->io;
        }

        $objectFactory = new ObjectFactory();
        $this->io = $objectFactory->createFromConfig(self::CONFIG_ROUTER_IO, self::DEFAULT_ROUTER_IO, self::CLASS_ROUTER_IO);

        return $this->io;
    }

}