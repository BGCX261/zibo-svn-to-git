<?php

namespace zibo\core;

use zibo\library\ObjectFactory;

use zibo\ZiboException;

use \ReflectionException;
use \ReflectionMethod;

/**
 * Dispatcher of Request objects
 */
class Dispatcher {

    /**
     * Wildcard for automatic action translation
     * @var string
     */
    const ACTION_ASTERIX = '*';

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
    const INTERFACE_CONTROLLER = 'zibo\\core\\Controller';

    /**
     * Created controller
     * @var zibo\core\Controller
     */
    protected $controller;

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
    public function __construct(ObjectFactory $objectFactory = null) {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Dispatches a request to the action of a controller
     * @param Request $request The request to dispatch
     * @param Response $response The response to dispatch the request to
     * @param boolean $checkReturnValue true to check the return value of the action for valid chaining values (null or Request),
     *                                  false for any return value
     * @return mixed The return value of the action
     * @throws zibo\ZiboException when checkReturnValue is set to true and the return value of the action is not null or a Request object
     */
    public function dispatch(Request $request, Response $response, $checkReturnValue = true) {
        $controller = $this->getController($request);
        $actionName = $request->getActionName();
        $parameters = $request->getParameters();

        $this->processActionName($controller, $actionName, $parameters);

        $this->prepareController($controller, $request, $response, $actionName, $parameters);

        $zibo = Zibo::getInstance();

        $zibo->runEvent(self::EVENT_PRE_DISPATCH, $controller, $actionName, $parameters);

        $controller->preAction();
        $returnValue = $this->invokeMethod($controller, $actionName, $parameters);
        $controller->postAction();

        $zibo->runEvent(self::EVENT_POST_DISPATCH, $controller, $actionName, $parameters);

        $this->controller = null;

        if ($checkReturnValue && !($returnValue === null || $returnValue instanceof Request)) {
            throw new ZiboException($actionName . ' in ' . $request->getControllerName() . ' should return null or a new zibo\\core\\Request instance');
        }

        return $returnValue;
    }

    /**
     * Sets a controller to the dispatcher. When a Request enters with the same controller class name
     * as the provided one, the provided controller will be used instead of creating a new one
     * @param Controller $controller
     * @return null
     */
    public function setController(Controller $controller) {
        $this->controller = $controller;
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
        if ($this->controller && $request->getControllerName() == get_class($this->controller)) {
            return $this->controller;
        }

        if ($this->objectFactory === null) {
            throw new ZiboException('Could not create controller ' . $request->getControllerName() . ': no ObjectFactory set');
        }

        try {
            $controller = $this->objectFactory->create($request->getControllerName(), self::INTERFACE_CONTROLLER);
        } catch (ZiboException $e) {
            throw new ZiboException('Could not create controller ' . $request->getControllerName(), 0, $e);
        }

        return $controller;
    }

    /**
     * Gets the method name from the action name. If the action name is *, a method name will
     * be looked via the parameters with a fallback on indexAction.
     * @param Controller $controller The controller to invoke
     * @param string $actionName The name of the action
     * @param array $parameters The parameters of the request
     * @return null
     * @throws zibo\ZiboException when no invokable action is found
     */
    protected function processActionName(Controller $controller, &$actionName, &$parameters) {
        if ($actionName != self::ACTION_ASTERIX) {
            $this->checkInvokable($controller, $actionName);
            return;
        }

        if (count($parameters) != 0) {
            $testActionName = $parameters[0] . self::ACTION_SUFFIX;
            try {
                $method = $this->checkInvokable($controller, $testActionName);
                $actionName = $testActionName;
                unset($parameters[0]);
                return;
            } catch (ZiboException $e) {
                $previous = $e->getPrevious();
            }
        }

        $this->checkInvokable($controller, self::ACTION_INDEX);
        $actionName = self::ACTION_INDEX;
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
        $controller->setRequest($request);
        $controller->setResponse($response);
    }

    /**
     * Invokes the provided method
     * @param object $object Object where the method will be invoked on
     * @param string $methodName Name of the method
     * @param array $args arguments for the method
     * @return mixed the return of the invoked method
     */
    protected function invokeMethod($object, $methodName, array $arguments = null) {
        $return = null;

        try {
            $method = new ReflectionMethod($object, $methodName);
            if ($arguments) {
                $return = $method->invokeArgs($object, $arguments);
            } else {
                $return = $method->invoke($object);
            }
        } catch (ReflectionException $e) {
        }

        return $return;
    }

    /**
     * Checks if the provided method is invokable
     * @param object $object Object of the method
     * @param string $methodName Name of the method
     * @return null
     * @throws zibo\ZiboException when the method is not invokable
     */
    protected function checkInvokable($object, $methodName) {
        try {
            $method = new ReflectionMethod($object, $methodName);
        } catch (ReflectionException $e) {
            throw new ZiboException('Could not dispatch action ' . $methodName . ' in ' . get_class($object), 0, $e);
        }
    }

}