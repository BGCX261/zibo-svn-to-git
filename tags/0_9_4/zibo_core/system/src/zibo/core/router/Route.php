<?php

/**
 * @package zibo-core-router
 */
namespace zibo\core\router;

use zibo\core\Dispatcher;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Route data container
 */
class Route {

    private $path;
    private $controllerClass;
    private $action;

    public function __construct($path, $controllerClass, $action = null) {
        $this->setPath($path);
        $this->setControllerClass($controllerClass);
        $this->setAction($action);
    }

    public function getPath() {
        return $this->path;
    }

    public function getControllerClass() {
        return $this->controllerClass;
    }

    public function getAction() {
        return $this->action;
    }

    private function setPath($path) {
        if (String::isEmpty($path)) {
            throw new ZiboException('path is empty');
        }

        $regexHttpSegment = '(([a-zA-Z0-9]|[$+_.-]|[!*\'(),])|(%[0-9A-Fa-f][0-9A-Fa-f])|[;:@&=])*';
        $regexHttpPath = '/^' . $regexHttpSegment . '(\\/' . $regexHttpSegment . ')*$/';
        if (!preg_match($regexHttpPath, $path)) {
            throw new ZiboException($path . ' is not a valid http path');
        }

        $this->path = $path;
    }

    private function setControllerClass($controllerClass) {
        if (String::isEmpty($controllerClass)) {
            throw new ZiboException('controllerClass is empty');
        }
        $this->controllerClass = $controllerClass;
    }

    private function setAction($action) {
        if (String::isEmpty($action)) {
            $action = Dispatcher::ACTION_ASTERIX;
        }
        $this->action = $action;
    }

}