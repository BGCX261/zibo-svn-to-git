<?php

namespace zibo\core\dispatcher;

use zibo\core\controller\Controller;
use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

use zibo\library\Callback;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Generic dispatcher for request objects
 */
class GenericDispatcher implements Dispatcher {

    /**
	 * Suffix for parameter to method name translation
	 * @var string
	 */
    const ACTION_SUFFIX = 'Action';

    /**
	 * Method name of the index action
	 * @var string
	 */
    const ACTION_INDEX = 'indexAction';

    /**
     * Event name run right before dispatching a controller
     * @var string
     */
    const EVENT_PRE_DISPATCH = 'dispatcher.dispatch.pre';

    /**
     * Event name run right after dispatching a controller
     * @var string
     */
    const EVENT_POST_DISPATCH = 'dispatcher.dispatch.post';

    /**
     * Class name of the controller interface
     * @var string
     */
    const INTERFACE_CONTROLLER = 'zibo\\core\\controller\\Controller';

    /**
     * Instance of Zibo
     * @var zibo\core\Zibo
     */
    protected $zibo;

    /**
     * Object factory to create controllers
     * @var zibo\library\ObjectFactory
     */
    protected $objectFactory;

    /**
     * Creates a new dispatcher
     * @param zibo\library\ObjectFactory $objectFactory
     * @return null
     */
    public function __construct(Zibo $zibo, ObjectFactory $objectFactory) {
        $this->zibo = $zibo;
        $this->objectFactory = $objectFactory;
    }

    /**
     * Dispatches a request to the action of a controller
     * @param Request $request The request to dispatch
     * @param Response $response The response to dispatch the request to
     * @return mixed The return value of the action
     * @throws zibo\ZiboException when the action is not invokable
     */
    public function dispatch(Request $request, Response $response) {
        $controller = $this->getController($request);
        $actionName = $request->getActionName();
        $parameters = $request->getParameters();

        $callback = $this->processAction($controller, $actionName, $parameters);

        $this->prepareController($controller, $request, $response, $actionName, $parameters);

        $this->zibo->triggerEvent(self::EVENT_PRE_DISPATCH, $controller, $actionName, $parameters);

        $controller->preAction();
        $returnValue = $callback->invokeWithArrayArguments($parameters);
        $controller->postAction();

        $this->zibo->triggerEvent(self::EVENT_POST_DISPATCH, $controller, $actionName, $parameters);

        return $returnValue;
    }

    /**
     * Gets the controller of a request. If the controller class name is the same as the clasa name
     * of the controller which is set through setController, the set controller will be used instead
     * of creating a new one.
     * @param Request $request
     * @return Controller
     * @throws zibo\ZiboException when no object factory has been set
     * @throws zibo\ZiboException when the controller could not be created
     */
    protected function getController(Request $request) {
        try {
            $controller = $this->objectFactory->create($request->getControllerName(), self::INTERFACE_CONTROLLER);
        } catch (ZiboException $exception) {
            throw new ZiboException('Could not create controller ' . $request->getControllerName(), 0, $exception);
        }

        return $controller;
    }

    /**
     * Gets the method name from the action name. If the action name is *, a
     * method name will be looked via the parameters with a fallback on
     * indexAction.
     * @param Controller $controller The controller to invoke
     * @param string $actionName The name of the action
     * @param array $parameters The parameters of the request
     * @return null
     * @throws zibo\ZiboException when no invokable action is found
     */
    protected function processAction(Controller $controller, &$actionName, &$parameters) {
        if ($actionName !== null) {
            return $this->getCallback($controller, $actionName);
        }

        if (count($parameters) != 0) {
            $testActionName = $parameters[0] . self::ACTION_SUFFIX;
            try {
                $callback = $this->getCallback($controller, $testActionName);
                $actionName = $testActionName;
                unset($parameters[0]);

                return $callback;
            } catch (ZiboException $e) {

            }
        }

        $actionName = self::ACTION_INDEX;
        return $this->getCallback($controller, self::ACTION_INDEX);
    }

    /**
     * Gets the callback for the provided method
     * @param object $object Object of the method
     * @param string $methodName Name of the method
     * @return zibo\library\Callback
     * @throws zibo\ZiboException when the method is not invokable
     */
    protected function getCallback($object, $methodName) {
        try {
            $callback = new Callback(array($object, $methodName));
            if (!$callback->isCallable()) {
                throw new ZiboException('Could not invoke action ' . $methodName . ' in ' . get_class($object));
            }
        } catch (ZiboException $exception) {
            throw new ZiboException('Could not dispatch action ' . $methodName . ' in ' . get_class($object), 0, $exception);
        }

        return $callback;
    }

    /**
     * Prepares the controller
     * @param Controller $controller The controller to prepare
     * @param Request $request The request for the controller
     * @param Response $response The response for the controller
     * @param string $actionName The method which will be invoked
     * @param array $parameters The parameters for that method
     * @return null
     */
    protected function prepareController(Controller $controller, Request $request, Response $response, $actionName, array $parameters) {
        $controller->setZibo($this->zibo);
        $controller->setRequest($request);
        $controller->setResponse($response);
    }

}