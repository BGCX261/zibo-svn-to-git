<?php

namespace zibo\core\controller;

use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

/**
 * Interface for a controller of an action
 */
interface Controller {

    /**
     * Sets the instance of Zibo to this controller
     * @param zibo\core\Zibo $zibo The instance of Zibo
     * @return null
     */
    public function setZibo(Zibo $zibo);

    /**
     * Sets the request for this controller
     * @param zibo\core\Request $request The request
     * @return null
     */
    public function setRequest(Request $request);

    /**
     * Sets the response for this controller
     * @param zibo\core\Response $response The response
     * @return null
     */
    public function setResponse(Response $response);

    /**
     * Hook to execute before every action
     * @return null
     */
    public function preAction();

    /**
     * Hook to execute after every action
     * @return null
     */
    public function postAction();

}