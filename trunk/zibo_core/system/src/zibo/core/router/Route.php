<?php

namespace zibo\core\router;

use zibo\library\String;

use zibo\ZiboException;

/**
 * A route maps a URL path to a controller class and action method.
 */
class Route {

    /**
     * URL path to the controller
     * @var string
     */
    private $path;

    /**
     * Full name of the controller class (including namespace)
     * @var string
     */
    private $controllerClass;

    /**
     * Name of the action method in the controller class
     * @var string
     */
    private $action;

    /**
     * Constructs a new route
     * @param string $path URL path to the controller
     * @param string $controllerClass Full name of the controller class
     * (including namespace)
     * @param string $action Name of the action method in the controller class
     * @return null
     */
    public function __construct($path, $controllerClass, $action = null) {
        $this->setPath($path);
        $this->setControllerClass($controllerClass);
        $this->setAction($action);
    }

    /**
     * Sets the URL path
     * @param string $path
     * @return null
     * @throws zibo\ZiboException when the path is empty or invalid
     */
    private function setPath($path) {
        self::validatePath($path);

        $this->path = $path;
    }

    /**
     * Gets the URL path
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Sets the name of the controller class.
     * @param string $controllerClass Full name of the controller class
     * (including namespace)
     * @return null
     * @throws zibo\ZiboException when the controller class is empty or invalid
     */
    private function setControllerClass($controllerClass) {
        if (!String::isString($controllerClass, String::NOT_EMPTY)) {
            throw new ZiboException('Provided controller class name is empty or not a string');
        }

        $this->controllerClass = $controllerClass;
    }

    /**
     * Gets the name of the controller class.
     * @return string Full name of the controller class (including namespace)
     */
    public function getControllerClass() {
        return $this->controllerClass;
    }

    /**
     * Sets the action of the controller
     * @param string $action Name of the action method in the controller
     * @return null
     * @throws zibo\ZiboException when the provided action is empty or invalid
     */
    private function setAction($action) {
        if ($action !== null && !String::isString($action, String::NOT_EMPTY)) {
            throw new ZiboException('Provided action is empty or not a string');
        }

        $this->action = $action;
    }

    /**
     * Gets the action of the controller
     * @return string Name of the action method in the controller
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Validates a HTTP path
     * @param string $path
     * @return null
     * @throws ZiboException when the path is invalid
     */
    public static function validatePath($path) {
        if (!String::isString($path, String::NOT_EMPTY)) {
            throw new ZiboException('Provided path is empty or not a string.');
        }

        $regexHttpSegment = '(([a-zA-Z0-9]|[$+_.-]|[!*\'(),])|(%[0-9A-Fa-f][0-9A-Fa-f])|[;:@&=])*';
        $regexHttpPath = '/^' . $regexHttpSegment . '(\\/' . $regexHttpSegment . ')*$/';

        if (!preg_match($regexHttpPath, $path)) {
            throw new ZiboException($path . ' is not a valid HTTP path');
        }
    }

}