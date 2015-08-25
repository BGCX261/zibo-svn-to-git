<?php

namespace zibo\core;

/**
 * Data container of a request
 */
class Request {

    /**
     * Separator for the different tokens in a request
     * @var string
     */
    const QUERY_SEPARATOR = '/';

    /**
     * Name of the accept header
     * @var string
     */
    const HEADER_ACCEPT = 'HTTP_ACCEPT';

    /**
     * Name of the accept language header
     * @var string
     */
    const HEADER_ACCEPT_LANGUAGE = 'HTTP_ACCEPT_LANGUAGE';

    /**
     * Name of the accept charset header
     * @var string
     */
    const HEADER_ACCEPT_CHARSET = 'HTTP_ACCEPT_CHARSET';

    /**
     * Name of the accept encoding header
     * @var string
     */
    const HEADER_ACCEPT_ENCODING = 'HTTP_ACCEPT_ENCODING';

    /**
     * Name of the modified since header
     * @var string
     */
    const HEADER_MODIFIED_SINCE = 'HTTP_IF_MODIFIED_SINCE';

    /**
     * Name of the request with header
     * @var string
     */
    const HEADER_REQUEST = 'HTTP_X_REQUESTED_WITH';

    /**
     * Name of the user agent header
     * @var string
     */
    const HEADER_USER_AGENT = 'HTTP_USER_AGENT';

    /**
     * Value of the request header when the request is a XML HTTP request
     * @var string
     */
    const XML_HTTP_REQUEST = 'XMLHttpRequest';

    /**
     * The base url of the request (or of this installation)
     * @var string
     */
    private $baseUrl;

    /**
     * The base url to the controller
     * @var string
     */
    private $basePath;

    /**
     * The route to the controller. This is the basePath without the baseUrl
     * @var string
     */
    private $route;

    /**
     * The full name of the controller class (with namespace)
     * @var string
     */
    private $controllerName;

    /**
     * The name of the action method in the controller Class
     * @var string
     */
    private $actionName;

    /**
     * The parameters for the action method
     * @var array
     */
    private $parameters;

    /**
     * Construct a new request
     * @param string $baseUrl the base url of the request
     * @param string $basePath the base url to the controller of the action
     * @param string $controllerName the full name of the controller class (including namespace)
     * @param string $actionName the action method in the controller class
     * @param array $parameters an array containing the parameters for the action method
     * @return null
     */
    public function __construct($baseUrl, $basePath, $controllerName, $actionName, array $parameters = array()) {
        $this->baseUrl = $baseUrl;
        $this->basePath = $basePath;
        $this->route = str_replace($this->baseUrl, '', $this->basePath);
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->parameters = $parameters;
    }

    /**
     * Get the base url of this request (or installation)
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * Get the base url to the controller of the action
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * Get the route to the controller. This is the basePath without the baseUrl
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Get the full class name (including namespace) of the controller
     * @return string
     */
    public function getControllerName() {
        return $this->controllerName;
    }

    /**
     * Get the method name of the action in the controller
     * @return string
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * Get the parameters for the action method
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Get the parameters for the action as a string separated by /. Useful to recreate the url
     * @return string
     */
    public function getParametersAsString() {
        if (!$this->parameters) {
            return '';
        }

        return self::QUERY_SEPARATOR . implode(self::QUERY_SEPARATOR, $this->parameters);
    }

    /**
     * Gets the method of the request (GET, POST, ...)
     * @return string
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Gets a HTTP header, use the HEADER constants of this class if available.
     * @param string $name Name of the header
     * @return string Value of the header
     */
    public function getHeader($name) {
        if (!array_key_exists($name, $_SERVER)) {
            return null;
        }

        return $_SERVER[$name];
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     * Taken from the Zend framework
     * @return boolean
     */
    public function isXmlHttpRequest() {
        if (!array_key_exists(self::HEADER_REQUEST, $_SERVER)) {
            return false;
        }

        return $_SERVER[self::HEADER_REQUEST] == self::XML_HTTP_REQUEST;
    }

}