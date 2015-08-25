<?php

namespace zibo\library\widget;

use zibo\core\Controller;
use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Response;

use zibo\library\widget\controller\Widget;

use zibo\ZiboException;

/**
 * Dispatcher for widgets
 */
class WidgetDispatcher extends Dispatcher {

    /**
     * The widget to dispatch
     * @var zibo\library\widget\controller\Widget
     */
    private $widget;

    /**
     * Flag to see if the request parameters are for the containing widget
     * @var boolean
     */
    private $passRequestParameters;

    /**
     * Sets the widget to dispatch
     * @param zibo\library\widget\controller\Widget $widget
     * @returnnull
     */
    public function setWidget(Widget $widget) {
        $this->widget = $widget;
    }

    /**
     * Gets the set widget
     * @param zibo\core\Request $request Not used
     * @return zibo\core\Controller
     * @throws zibo\ZiboException when no widget has been set
     */
    protected function getController(Request $request) {
        if (!$this->widget) {
            throw new ZiboException('No widget set');
        }

        return $this->widget;
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
        $this->passRequestParameters = true;

        $action = null;
        if ($actionName == Dispatcher::ACTION_ASTERIX && $parameters) {
            $action = $parameters[0];
        }

        $widgetParameters = $this->widget->getRequestParameters();

        if ($action && ($widgetParameters != Dispatcher::ACTION_ASTERIX && !in_array($action, $widgetParameters))) {
            $actionName = Dispatcher::ACTION_INDEX;
            $parameters = array();
            $this->passRequestParameters = false;
        }

        parent::processActionName($this->widget, $actionName, $parameters);
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
        if (!$this->passRequestParameters) {
            $request = new Request($request->getBaseUrl(), $request->getBasePath(), $request->getControllerName(), Dispatcher::ACTION_INDEX);
        }

        $this->widget->setRequest($request);
        $this->widget->setResponse($response);
    }

}