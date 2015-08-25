<?php

/**
 * @package zibo-log-debug
 */
namespace zibo\log\debug;

use zibo\core\Dispatcher;
use zibo\core\Zibo;

use zibo\library\filesystem\Formatter;

use zibo\log\LogItem;

/**
 * Log debug values
 */
class Module {

    const LOG_NAME = 'debug';

    public function initialize() {
        $this->registerEvents();
    }

    private function registerEvents() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Zibo::EVENT_PRE_ROUTE, array($this, 'logRoutePreFirst'), 0);
        $zibo->registerEventListener(Zibo::EVENT_PRE_ROUTE, array($this, 'logRoutePreLast'), 99);
        $zibo->registerEventListener(Zibo::EVENT_POST_ROUTE, array($this, 'logRoutePost'));
        $zibo->registerEventListener(Dispatcher::EVENT_PRE_DISPATCH, array($this, 'logDispatchPre'));
        $zibo->registerEventListener(Dispatcher::EVENT_POST_DISPATCH, array($this, 'logDispatchPost'));
        $zibo->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'logResponsePre'));
        $zibo->registerEventListener(Zibo::EVENT_POST_RESPONSE, array($this, 'logResponsePost'));
    }

    public function logRoutePreFirst() {
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Router', 'Routing request...', LogItem::INFORMATION, self::LOG_NAME);
    }

    public function logRoutePreLast() {
        $zibo = Zibo::getInstance();

        $router = $zibo->getRouter();
        if (!$router) {
            $message = 'No router set, using default router';
        } else {
            $message = 'Using router ' . get_class($router);
        }

        $zibo->runEvent(Zibo::EVENT_LOG, 'Router', $message, LogItem::INFORMATION, self::LOG_NAME);
    }

    public function logRoutePost() {
        $zibo = Zibo::getInstance();

        $request = $zibo->getRequest();
        if ($request == null) {
	        $zibo->runEvent(Zibo::EVENT_LOG, 'Router', 'no request recieved', LogItem::WARNING, self::LOG_NAME);
            return;
        }

        $parameters = $request->getParameters();
        $route = $request->getRoute();
        $controller = $request->getControllerName();
        $action = $request->getActionName() . '(' . implode(', ', $parameters) . ')';

        $zibo->runEvent(Zibo::EVENT_LOG, 'Router', $route . ' routed to ' . $controller . '->' . $action, LogItem::INFORMATION, self::LOG_NAME);
    }

    public function logDispatchPre($controller, $actionName, $parameters) {
        if (!empty($parameters)) {
           $parameters = '(' . implode(', ', $parameters) . ')';
        } else {
            $parameters = '()';
        }
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Dispatcher', 'invoking ' . get_class($controller) . '->' . $actionName . $parameters, LogItem::INFORMATION, self::LOG_NAME);
    }

    public function logDispatchPost($controller, $actionName, $parameters) {
        if (!empty($parameters)) {
           $parameters = '(' . implode(', ', $parameters) . ')';
        } else {
            $parameters = '()';
        }
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Dispatcher', 'invoked ' . get_class($controller) . '->' . $actionName . $parameters, LogItem::INFORMATION, self::LOG_NAME);
    }

    public function logResponsePre() {
        $zibo = Zibo::getInstance();

        $response = $zibo->getResponse();
        $code = $response->getStatusCode();
        $headers = $response->getHeaders();
        $view = $response->getView();

        $zibo->runEvent(Zibo::EVENT_LOG, 'Sending response', 'code ' . $code, LogItem::INFORMATION, self::LOG_NAME);
        foreach ($headers as $header) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'Sending header', $header, LogItem::INFORMATION, self::LOG_NAME);
        }
        if ($view) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'Rendering view', get_class($view), LogItem::INFORMATION, self::LOG_NAME);
        }
    }

    public function logResponsePost() {
        $zibo = Zibo::getInstance();
        $zibo->runEvent(Zibo::EVENT_LOG, 'Response sent', '', LogItem::INFORMATION, self::LOG_NAME);

        $memoryUsage = Formatter::formatSize(memory_get_peak_usage());
        $zibo->runEvent(Zibo::EVENT_LOG, 'Maximum memory usage was ' . $memoryUsage, '', LogItem::INFORMATION, self::LOG_NAME);
    }

}