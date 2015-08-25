<?php

namespace zibo\core\controller;

use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

use zibo\ZiboException;

/**
 * Abstract implementation of a controller
 */
class AbstractController implements Controller {

    /**
     * The instance of Zibo
     * @var zibo\core\Zibo
     */
    protected $zibo;

    /**
     * The request for this controller
     * @var zibo\core\Request
     */
    protected $request;

    /**
     * The response for this controller
     * @var zibo\core\Response
     */
    protected $response;

    /**
     * Sets the instance of Zibo to this controller
     * @param zibo\core\Zibo $zibo The instance of Zibo
     * @return null
     */
    public function setZibo(Zibo $zibo) {
        $this->zibo = $zibo;
    }

    /**
     * Sets the request for this controller
     * @param zibo\core\Request $request The request
     * @return null
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * Sets the response for this controller
     * @param zibo\core\Response $response The response
     * @return null
     */
    public function setResponse(Response $response) {
        $this->response = $response;
    }

    /**
     * Hook to execute before every action
     * @return null
     */
    public function preAction() {

    }

    /**
     * Hook to execute after every action
     * @return null
     */
    public function postAction() {

    }

    /**
     * Gets a new request for chaining based on the provided arguments
     * @param string $controllerClass Full class name of the controller for the new request
     * @param string|null $action Action method in the controller
     * @param boolean|int|array $parameters provide an array as parameters for
     * the new request. If a boolean is provided, the parameters will be taken
     * from the request. Set the boolean to true and the first parameter will
     * be taken of the parameter array and added to the base path. You can also
     * provide the number of parameters to be taken of the parameter array and
     * added to the base path.
     * @param string $basePath the basePath for your new request. if none
     * specified, the base path will be taken from the current request
     * @return zibo\core\Request
     */
    protected function forward($controllerClass, $action = null, $parameters = true, $basePath = null) {
        $baseUrl = $this->request->getBaseUrl();
        if (!$basePath) {
            $basePath = $this->request->getBasePath();
        }

        if (!is_array($parameters)) {
            $requestParameters = $this->request->getParameters();

            if (is_bool($parameters) && $parameters) {
                $parameters = 1;
            }

            if (is_numeric($parameters) && $parameters > 0) {
                for ($i = 0; $i < $parameters; $i++) {
                    $basePathSuffix = array_shift($requestParameters);
                    $basePath .= Request::QUERY_SEPARATOR . $basePathSuffix;
                }
            }

            $parameters = $requestParameters;
        }

        return new Request($baseUrl, $basePath, $controllerClass, $action, $parameters, $this->request->getQueryParameters(), $this->request->getBodyParameters());
    }

    /**
     * Gets a request from the provided path for request chaining
     * @param string $path The path to route
     * @return zibo\core\Request|null A request if the path was found, null otherwise
     */
    protected function route($path) {
        $router = $this->zibo->getRouter();
        if (!$router) {
            return null;
        }

        return $router->getRequest($this->request->getBaseUrl(), $path);
    }

    /**
     * Parses an array of values into a key value array. Usefull to parse the
     * arguments of an action
     *
     * array(
     * &nbsp;&nbsp;&nbsp;&nbsp;'key1',
     * &nbsp;&nbsp;&nbsp;&nbsp;'value1',
     * &nbsp;&nbsp;&nbsp;&nbsp;'key2',
     * &nbsp;&nbsp;&nbsp;&nbsp;'value2'
     * )
     * will return
     * array(
     * &nbsp;&nbsp;&nbsp;&nbsp;'key1' => 'value1',
     * &nbsp;&nbsp;&nbsp;&nbsp;'key2' => 'value2'
     * )
     * @param array $arguments Arguments array
     * @return array Parsed arguments array
     * @throws zibo\ZiboException when the number of elements in the argument
     * array is not even
     */
    protected function parseArguments(array $arguments) {
        if (count($arguments) % 2 != 0) {
            throw new ZiboException('Provided arguments array should have an even number of arguments');
        }

        $parsedArguments = array();

        $argumentName = null;
        foreach ($arguments as $argument) {
            if ($argumentName === null) {
                $argumentName = $argument;
            } else {
                $parsedArguments[$argumentName] = $argument;
                $argumentName = null;
            }
        }

        return $parsedArguments;
    }

}