<?php

namespace zibo\admin;

use zibo\admin\controller\AuthenticationController;
use zibo\admin\message\Message;
use zibo\admin\view\BaseView;
use zibo\admin\view\ExceptionView;

use zibo\core\environment\CliEnvironment;
use zibo\core\router\GenericRouter;
use zibo\core\view\HtmlView;
use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\message\MessageList;
use zibo\library\security\authenticator\HttpAuthenticator;
use zibo\library\security\exception\AuthenticationException;
use zibo\library\security\exception\UnauthorizedException;
use zibo\library\security\SecurityManager;
use zibo\library\smarty\view\SmartyView;
use zibo\library\ObjectFactory;
use zibo\library\Session;

use \Exception;

/**
 * Admin module initializer
 */
class Module {

    /**
     * Configuration key to see if the page should be redirected to the home when the session is expired
     * @var string
     */
    const CONFIG_SESSION_EXPIRE_REDIRECT = 'system.session.expire.redirect';

    /**
     * Configuration key to set the default controller through the configuration
     * @var string
     */
    const CONFIG_CONTROLLER_DEFAULT = 'system.default.controller';

    /**
     * Class name of the authentication controller
     * @var string
     */
    const CONTROLLER_AUTHENTICATION = 'zibo\\admin\\controller\\AuthenticationController';

    /**
     * Class name of the default controller
     * @var string
     */
    const CONTROLLER_DEFAULT = 'zibo\\admin\\controller\\IndexController';

    /**
     * Class name of the controller which handles the web directory
     * @var string
     */
    const CONTROLLER_WEB = 'zibo\\admin\\controller\\WebController';

    /**
     * Route to the authentication controller
     * @var string
     */
    const ROUTE_AUTHENTICATION = 'authentication';

    /**
     * Route to the locales controller
     * @var unknown_type
     */
    const ROUTE_LOCALES = 'admin/locales';

    /**
     * Route to the localize controller
     * @var string
     */
    const ROUTE_LOCALIZE = 'localize';

    /**
     * Route to the sidebar controller
     * @var string
     */
    const ROUTE_SIDEBAR = 'ajax/sidebar';

    /**
     * Route to the modules controller
     * @var string
     */
    const ROUTE_MODULES = 'admin/modules';

    /**
     * Route to the profile action of the security controller
     * @var string
     */
    const ROUTE_PROFILE = 'profile';

    /**
     * Route to the security controller
     * @var string
     */
    const ROUTE_SECURITY = 'admin/security';

    /**
     * Route to the system controller
     * @var string
     */
    const ROUTE_SYSTEM = 'admin/system';

    /**
     * Session key to store the response messages
     * @var string
     */
    const SESSION_MESSAGES = 'response.messages';

    /**
     * Session key to store the referer
     * @var string
     */
    const SESSION_REFERER = 'referer';

    /**
     * Translation key of the forbidden error message
     * @var string
     */
    const TRANSLATION_ERROR_FORBIDDEN = 'security.error.forbidden';

    /**
     * Translation key of the unauthorized error message
     * @var string
     */
    const TRANSLATION_ERROR_UNAUTHORIZED = 'security.error.authentication';

    /**
     * Initializes the module for a request.
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Zibo::EVENT_ERROR, array($this, 'setExceptionView'));
        $zibo->registerEventListener(Zibo::EVENT_CLEAR_CACHE, array($this, 'clearCache'));
        $zibo->registerEventListener(Zibo::EVENT_POST_ROUTE, array($this, 'checkSecuredRoute'));
        $zibo->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'onPreResponse'));

        $this->setDefaultAction($zibo);
    }

    /**
     * Sets an exception view for the provided exception to the response.
     * @param Exception $exception
     * @return null
     */
    public function setExceptionView(Exception $exception) {
        if ($exception instanceof UnauthorizedException) {
            $this->showAuthenticationForm();
            return;
        }

        $zibo = Zibo::getInstance();

        $class = get_class($exception);
        $message = $exception->getMessage();
        $trace = $exception->getTraceAsString();

        // log the exception
        try {
            $zibo->runEvent(Zibo::EVENT_LOG, $class . ($message ? ': ' . $message : ''), $trace);
        } catch (Exception $e) {

        }

        // set server error response code
        $response = $zibo->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
        $response->clearRedirect();

        // show the exception
        if ($zibo->getEnvironment()->getName() == CliEnvironment::NAME) {
            // set a cli view of the exception
            $title = 'Uncaught exception (' . $class . ')' . ($message ? ': ' . $message : '');
            echo "\n" . $title . "\n\n" . $trace;
        } else {
            // set a web view of the exception
            $view = new ExceptionView($exception);
            $response->setView($view);
        }
    }

    /**
     * Checks if the current route is allowed. If not allowed, the response will be set to the
     * authentication form.
     * @return null
     */
    public function checkSecuredRoute() {
        $zibo = Zibo::getInstance();
        $request = $zibo->getRequest();

        if (!$request || $request->getControllerName() == self::CONTROLLER_WEB) {
            return;
        }

        $route = $request->getRoute() . $request->getParametersAsString();

        $zibo->runEvent(Zibo::EVENT_LOG, 'Checking for secured route', $route);
        if (SecurityManager::getInstance()->isRouteAllowed($route)) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'Route allowed', $route);
            return;
        }
        $zibo->runEvent(Zibo::EVENT_LOG, 'Route denied', $route);

        $this->setReferer();

        throw new UnauthorizedException();
    }

    /**
     * Processes the referer and the response messages before rendering the response
     * @return null
     */
    public function onPreResponse() {
        $this->handleReferer();
        $this->handleResponseMessages();
        $this->handleExpiredSession();
    }

    /**
     * Handles the referer. Stores the referer to the session if needed.
     * @return null
     */
    private function handleReferer() {
        $response = Zibo::getInstance()->getResponse();

        $view = $response->getView();
        if (!($view instanceof BaseView) || ($view instanceof ExceptionView)) {
            return;
        }

        $this->setReferer();
    }

    /**
     * Handles the response messages. If a redirect is detected, the messages are stored to the
     * session for a next request. If the view is a smarty view, the messages will be set to
     * the view. You can display them using the {messages} smarty function.
     * @return null
     */
    private function handleResponseMessages() {
        $response = Zibo::getInstance()->getResponse();
        $session = Session::getInstance();

        $messages = $session->get(self::SESSION_MESSAGES);
        if ($messages == null) {
            $messages = new MessageList();
        }

        if ($response->willRedirect()) {
            $messages->merge($response->getMessages());

            $session->set(self::SESSION_MESSAGES, $messages);
            return;
        }

        $view = $response->getView();
        if (!($view instanceof SmartyView)) {
            return;
        }

        $session->set(self::SESSION_MESSAGES);

        $messages->merge($response->getMessages());
        $view->set('_messages', $messages);
    }

    /**
     * Redirect the current page when the session is expired. This will only be done if the configuration key system.session.expire.redirect is set to true or to a URL
     * @return null
     */
    private function handleExpiredSession() {
        $zibo = Zibo::getInstance();

        $sessionExpire = $zibo->getConfigValue(self::CONFIG_SESSION_EXPIRE_REDIRECT);
        if (!$sessionExpire) {
            return;
        }

        $user = SecurityManager::getInstance()->getUser();
        if (!$user) {
            return;
        }

        $response = $zibo->getResponse();

        $view = $response->getView();
        if (!($view instanceof HtmlView)) {
            return;
        }

        $sessionTime = $zibo->getConfigValue(Session::CONFIG_SESSION_TIME);
        if (!$sessionTime) {
            return;
        }

        if (!is_bool($sessionExpire) && !is_numeric($sessionExpire)) {
            $url = $sessionExpire;
        } else {
            $url = $zibo->getRequest()->getBaseUrl();
        }
        $timeOut = ($sessionTime * 60000) + 1000;

        $view->addInlineJavascript('setTimeout(\'window.location = "' . $url . '"\', ' . $timeOut . ');');
    }

    /**
     * Stores the full URL of the current request as referer to the session
     * @return null
     */
    private function setReferer() {
        $request = Zibo::getInstance()->getRequest();

        $referer = $request->getBasePath() . $request->getParametersAsString();

        Session::getInstance()->set(self::SESSION_REFERER, $referer);
    }

    /**
     * Sets an unauthorized status code to the response and dispatch to the authentication form
     * @return null
     */
    private function showAuthenticationForm() {
        $zibo = Zibo::getInstance();
        $request = $zibo->getRequest();
        $response = $zibo->getResponse();

        $securityManager = SecurityManager::getInstance();

        $user = $securityManager->getUser();
        if ($user) {
            // already logged in, show blank page with error message

            $response->addMessage(new Message(self::TRANSLATION_ERROR_FORBIDDEN, Message::TYPE_ERROR));

            $response->setStatusCode(Response::STATUS_CODE_FORBIDDEN);
            $response->setView(new BaseView());

            return;
        }

        // not logged in, show authentication form

        $response->addMessage(new Message(self::TRANSLATION_ERROR_UNAUTHORIZED, Message::TYPE_ERROR));

        $authenticator = $securityManager->getAuthenticator();
        if ($authenticator instanceof HttpAuthenticator) {
            $response->addHeader(Response::HEADER_AUTHENTICATE, $authenticator->getAuthenticateHeader());
            $response->setStatusCode(Response::STATUS_CODE_UNAUTHORIZED);
        } else {
            $response->setStatusCode(Response::STATUS_CODE_FORBIDDEN);
        }

        $dispatcher = $zibo->getDispatcher();
        if (!$dispatcher) {
            $dispatcher = new Dispatcher(new ObjectFactory());
        }

        $baseUrl = $request->getBaseUrl();
        $basePath = $baseUrl . Request::QUERY_SEPARATOR . self::ROUTE_AUTHENTICATION;
        $controller = self::CONTROLLER_AUTHENTICATION;

        $request = new Request($baseUrl, $basePath, $controller, Dispatcher::ACTION_ASTERIX);

        $dispatcher->dispatch($request, $response);
    }

    /**
     * Sets the default action and controller to the router
     * @param zibo\core\Zibo $zibo
     * @return null
     */
    private function setDefaultAction(Zibo $zibo) {
        $router = $zibo->getRouter();
        if ($router === null) {
            $router = new GenericRouter();
            $zibo->setRouter($router);
        }

        if (!($router instanceof GenericRouter)) {
            return;
        }

        $defaultController = $router->getDefaultController();
        if ($defaultController) {
            return;
        }

        $defaultController = $zibo->getConfigValue(self::CONFIG_CONTROLLER_DEFAULT, self::CONTROLLER_DEFAULT);

        $router->setDefaultAction($defaultController);
    }

    /**
     * Clears the cache in the public directory
     * @return null
     */
    public function clearCache() {
        $root = Zibo::getInstance()->getRootPath();

        $publicCache = new File($root, Zibo::DIRECTORY_APPLICATION . '/' . Zibo::DIRECTORY_PUBLIC . '/' . Zibo::DIRECTORY_CACHE);
        $publicCache->delete();
    }

}