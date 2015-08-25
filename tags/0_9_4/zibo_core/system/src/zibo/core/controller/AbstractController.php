<?php

namespace zibo\core\controller;

use zibo\core\Controller;
use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

/**
 * Abstract implementation of a controller
 */
class AbstractController implements Controller {

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
     * Set the request for this controller
     * @param zibo\core\Request $request
     * @return null
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * Set the response for this controller
     * @param zibo\core\Response $response
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
     * Gets an argument from the query string
     * @param string $name The name of the argument
     * @param mixed $default Default value for when the argument is not set
     * @return mixed The value of the argument if set, the provided default value otherwise
     */
    protected function getArgument($name, $default = null) {
        $environment = Zibo::getInstance()->getEnvironment();
        return $environment->getArgument($name, $default);
    }

}