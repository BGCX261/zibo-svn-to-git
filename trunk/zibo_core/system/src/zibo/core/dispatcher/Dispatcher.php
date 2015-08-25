<?php

namespace zibo\core\dispatcher;

use zibo\core\Request;
use zibo\core\Response;

/**
 * Interface for a dispatcher of request objects
 */
interface Dispatcher {

    /**
     * Dispatches a request to the action of the controller
     * @param Request $request The request to dispatch
     * @param Response $response The response to dispatch the request to
     * @return mixed The return value of the action
     * @throws zibo\ZiboException when the action could not be invoked
     */
    public function dispatch(Request $request, Response $response);

}