<?php

namespace zibo\core;

use zibo\core\di\DependencyInjector;

use zibo\library\http\session\Session;
use zibo\library\http\Header;
use zibo\library\http\HeaderContainer;

/**
 * Represents the request (in mosts cases a HTTP request).
 */
class Request {

    /**
     * Class name of the session interface
     * @var string
     */
    const INTERFACE_SESSION = 'zibo\\library\\http\\session\\Session';

    /**
     * The HEAD method
     * @var string
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * The GET method
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * The PUT method
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * The DELETE method
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * Separator for the different tokens in a request
     * @var string
     */
    const QUERY_SEPARATOR = '/';

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
     * The parameters in the query of the HTTP request (eg ?var1=value&var2=value)
     * @var array
     */
    private $queryParameters;

    /**
     * The parameters in the body of the HTTP request
     * @var array
     */
    private $bodyParameters;

    /**
     * Container with the request headers
     * @var zibo\library\http\HeaderContainer
     */
    private $headers;

    /**
     * Constructs a new request
     * @param string $baseUrl the base url of the request
     * @param string $basePath the base url to the controller of the action
     * @param string $controllerName the full name of the controller class
     * (including namespace)
     * @param string $actionName the action method in the controller class
     * @param array $parameters an array containing the parameters for the
     * action method
     * @param array $queryParameters An array containing the parameters of the
     * query of the HTTP request (usually GET variables)
     * @param array $bodyParameters An array containing the parameters of the
     * body of the HTTP request (usually POST variables)
     * @return null
     */
    public function __construct($baseUrl, $basePath, $controllerName, $actionName = null, array $parameters = array(), array $queryParameters = array(), array $bodyParameters = array()) {
        $this->baseUrl = $baseUrl;
        $this->basePath = $basePath;
        $this->route = str_replace($this->baseUrl, '', $this->basePath);
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->parameters = $parameters;
        $this->queryParameters = $queryParameters;
        $this->bodyParameters = $bodyParameters;
    }

    /**
     * Gets a string representation of this request
     * @return string
     */
    public function __toString() {
        $string = $this->basePath . $this->getParametersAsString();

        if ($this->queryParameters) {
            $string .= '?' . $this->getQueryParametersAsString();
        }

        return $string;
    }

    /**
     * Gets the base URL of this request (or installation)
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * Gets the base URL to the controller of the action
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * Gets the route to the controller. This is the base path without the
     * base URL.
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Gets the full class name (including namespace) of the controller
     * @return string
     */
    public function getControllerName() {
        return $this->controllerName;
    }

    /**
     * Gets the method name of the action in the controller
     * @return string
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * Gets the parameters for the action method
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Get the parameters for the action as a string separated by /. Useful
     * to recreate the URL of this request
     * @return string
     */
    public function getParametersAsString() {
        $string = '';

        foreach ($this->parameters as $parameter) {
            $string .= self::QUERY_SEPARATOR . urlencode($parameter);
        }

        return $string;
    }

    /**
     * Gets a query parameter by name
     * @param string $name The name of the parameter
     * @param mixed $default Default value for the parameter
     * @return mixed The value of the query parameter if set, the provided
     * default otherwise
     */
    public function getQueryParameter($name, $default = null) {
        return $this->getParameterByName($this->queryParameters, $name, $default);
    }

    /**
     * Gets all the query parameters
     * @return array
     */
    public function getQueryParameters() {
        return $this->queryParameters;
    }

    /**
     * Gets the query parameters for the action as a string. Useful to recreate
     * the URL of this request. The question mark is not included.
     * @return string
    */
    public function getQueryParametersAsString() {
        $string = '';

        foreach ($this->queryParameters as $key => $value) {
            $string .= $key . '=' . urlencode($value);
        }

        return $string;
    }

    /**
     * Gets a body parameter by name
     * @param string $name The name of the parameter
     * @param mixed $default Default value for the parameter
     * @return mixed The value of the query parameter if set, the provided
     * default otherwise
     */
    public function getBodyParameter($name, $default = null) {
        return $this->getParameterByName($this->bodyParameters, $name, $default);
    }

    /**
     * Gets all the query parameters
     * @return array
     */
    public function getBodyParameters() {
        return $this->bodyParameters;
    }

    /**
     * Gets a parameter by name
     * @param array $parameters The parameters
     * @param string $name The name of the parameter
     * @param mixed $default Default value for the parameter
     * @return mixed The value of the query parameter if set, the provided
     * default otherwise
     */
    private function getParameterByName(array $parameters, $name, $default = null) {
        if (!String::isString($name, String::NOT_EMPTY)) {
            throw new ZiboException('Invalid parameter name provided');
        }

        if (!isset($parameters[$name])) {
            return $default;
        }

        return $parameters[$name];
    }

    /**
     * Sets the session container
     * @param zibo\library\http\session\Session $session
     * @return null
     */
    public function setSession(Session $session) {
        $this->session = $session;
    }

    /**
     * Checks if a session has been initiated
     * @return boolean
     */
    public function hasSession() {
        return empty($this->session);
    }

    /**
     * Gets the session container
     * @return zibo\library\http\session\Session
     */
    public function getSession() {
        if (!isset($this->session)) {
            $di = new DependencyInjector();
            $this->session = $di->getDependency(self::INTERFACE_SESSION);
        }

        return $this->session;
    }

    /**
     * Gets the method of this HTTP request (GET, POST, ...)
     * @return string
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Gets a HTTP header value
     * @param string $name Name of the header
     * @return string|array|null The value of the header, an array of values if
     * the header is set multiple times, null if not set
     * @see zibo\library\http\Header
     */
    public function getHeader($name) {
        if (!$this->headers) {
            $this->headers = new HeaderContainer();
            $this->headers->setHeadersFromServerRequest();
        }

        if (!$this->headers->hasHeader($name)) {
            return null;
        }

        $header = $this->headers->getHeader($name);

        if (!is_array($header)) {
            return $header->getValue();
        }

        $values = array();
        foreach ($header as $h) {
            $values[] = $h->getValue();
        }

        return $values;
    }

    /**
     * Gets a list of media types acceptable by the client browser.
     * @return array Array with the media type as key and the preferable order
     * as value
     */
    public function getAccept() {
        if (isset($this->accept)) {
            return $this->accept;
        }

        $header = $this->getHeader(Header::HEADER_ACCEPT);
        if (!$header) {
            return $this->accept = array();
        }

        return $this->accept = Header::parseAccept($header);
    }

    /**
     * Gets a list of charsets acceptable by the client browser.
     * @return array Array with the charset as key and the preferable order
     * as value
     */
    public function getAcceptCharset() {
        if (isset($this->acceptCharset)) {
            return $this->acceptCharset;
        }

        $header = $this->getHeader(Header::HEADER_ACCEPT_CHARSET);
        if (!$header) {
            return $this->acceptCharset = array();
        }

        return $this->acceptCharset = Header::parseAccept($header);
    }

    /**
     * Gets a list of encodings acceptable by the client browser.
     * @return array Array with the encoding as key and the preferable order
     * as value
     */
    public function getAcceptEncoding() {
        if (isset($this->acceptEncoding)) {
            return $this->acceptEncoding;
        }

        $header = $this->getHeader(Header::HEADER_ACCEPT_ENCODING);
        if (!$header) {
            return $this->acceptEncoding = array();
        }

        return $this->acceptEncoding = Header::parseAccept($header);
    }

    /**
     * Gets a list of languages acceptable by the client browser.
     * @return array Array with the language as key and the preferable order
     * as value
     */
    public function getAcceptLanguage() {
        if (isset($this->acceptLanguage)) {
            return $this->acceptLanguage;
        }

        $header = $this->getHeader(Header::HEADER_ACCEPT_LANGUAGE);
        if (!$header) {
            return $this->acceptLanguage = array();
        }

        return $this->acceptLanguage = Header::parseAccept($header);
    }

    /**
     * Gets the
     * Enter description here ...
     * @return Ambigous <string, multitype:, NULL, multitype:NULL >
     */
    public function getIfNoneMatch() {
        if (isset($this->ifNoneMatch)) {
            return $this->ifNoneMatch;
        }

        $header = $this->getHeader(Header::HEADER_IF_NONE_MATCH);
        if (!$header) {
            return $this->ifNoneMatch = array();
        }

        return $this->ifNoneMatch = Header::parseIfMatch($header);
    }

    /**
     * Gets the timestamp of the conditional modified since header
     * @return integer|null The timestamp if the header was set, null otherwise
     */
    public function getIfModifiedSince() {
        if (isset($this->ifModifiedSince)) {
            return $this->ifModifiedSince;
        }

        $header = $this->getHeader(Header::HEADER_IF_MODIFIED_SINCE);
        if (!$header) {
            return $this->ifModifiedSince = null;
        }

        return $this->ifModifiedSince = Header::parseTime($header);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     * Taken from the Zend framework
     * @return boolean
     */
    public function isXmlHttpRequest() {
        $header = $this->getHeader(Header::HEADER_REQUEST_WITH);
        if (!$header) {
            return false;
        }

        return $header == self::XML_HTTP_REQUEST;
    }

}