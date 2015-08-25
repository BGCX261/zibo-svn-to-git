<?php

namespace zibo\core;

/**
 * Interface for a controller of an action
 */
interface Controller {

    /**
     * Set the request for this controller
     * @param zibo\core\Request $request
     * @return null
     */
    public function setRequest(Request $request);

    /**
     * Set the response for this controller
     * @param zibo\core\Response $response
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