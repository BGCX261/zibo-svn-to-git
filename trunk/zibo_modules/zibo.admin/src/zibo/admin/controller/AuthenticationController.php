<?php

namespace zibo\admin\controller;

use zibo\admin\Module;
use zibo\admin\form\AuthenticationForm;
use zibo\admin\view\security\AuthenticationView;

use zibo\core\Request;

use zibo\library\security\exception\AuthenticationException;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

/**
 * Controller to authenticate a user with the system
 */
class AuthenticationController extends AbstractController {

    /**
     * Name of the login action
     * @var string
     */
    const ACTION_LOGIN = 'login';

    /**
     * Name of the logout action
     * @var string
     */
    const ACTION_LOGOUT = 'logout';

    /**
     * Session key for the referer to the login action
     * @var string
     */
    const SESSION_REFERER = 'referer.authentication';

    /**
     * Instance of the security manager
     * @var zibi\library\security\SecurityManager
     */
    private $securityManager;

    /**
     * Sets the security manager to this controller
     * @return null
     */
    public function preAction() {
        $this->securityManager = SecurityManager::getInstance();
    }

    /**
     * Default action of the controller, will perform the loginAction
     * @return null
     * @see loginAction
     */
    public function indexAction() {
        return $this->loginAction();
    }

    /**
     * Action to login a user with username and password authentication
     * @return null
     */
    public function loginAction() {
        $user = $this->securityManager->getUser();
        if ($user !== null) {
            $this->response->setRedirect($this->request->getBaseUrl());
            return;
        }

        $form = new AuthenticationForm($this->request->getBasePath() . Request::QUERY_SEPARATOR . self::ACTION_LOGIN);
        if (!$form->isSubmitted()) {
            $this->setSecurityReferer();

            $this->setAuthenticationView($form);
            return;
        }

        if ($form->isCancelled()) {
            $this->response->setRedirect($this->getSecurityReferer());
            return;
        }

        try {
            $form->validate();

            $username = $form->getUsername();
            $password = $form->getPassword();

            $this->securityManager->login($username, $password);

            $this->response->setRedirect($this->getSecurityReferer());
            return;
        } catch (AuthenticationException $exception) {
            if ($exception->getField() == null) {
                throw $exception;
            }

            $validationError = new ValidationError($exception->getTranslationKey(), $exception->getMessage());

            $validationException = new ValidationException();
            $validationException->addErrors($exception->getField(), array($validationError));

            $form->setValidationException($validationException);
        } catch (ValidationException $validationException) {

        }

        $this->setAuthenticationView($form);
    }

    /**
     * Action to logout the current user.
     * @return null
     */
    public function logoutAction() {
        $this->securityManager->logout();

        $this->clearReferer();

        $this->response->setRedirect($this->request->getBaseUrl());
    }

    /**
     * Sets the authentication view to the response
     * @param zibo\admin\form\AuthenticationForm $form Authentication form for the view
     * @return null
     */
    private function setAuthenticationView(AuthenticationForm $form) {
        $view = new AuthenticationView($form);
        $this->response->setView($view);
    }

    /**
     * Sets the referer to redirect to when performing a login action
     * @return null
     */
    private function setSecurityReferer() {
        $session = $this->getSession();

        $referer = $session->get(Module::SESSION_REFERER);
        if ($referer == null || preg_match('/' . Module::ROUTE_AUTHENTICATION . '/', $referer)) {
            $referer = null;
        }

        $session->set(self::SESSION_REFERER, $referer);
    }

    /**
     * Gets the referer to redirect to when performing a login action, when not set the base URL will be returned
     * @return string URL to redirect to
     */
    private function getSecurityReferer() {
        $session = $this->getSession();

        $referer = $session->get(self::SESSION_REFERER);

        if (!$referer) {
            $referer = $this->request->getBaseUrl();
        }

        return $referer;
    }

    /**
     * Clears the referer of the authentication controller and the referer of Zibo
     * @return null
     */
    private function clearReferer() {
        $session = $this->getSession();

        $session->set(Module::SESSION_REFERER, null);
        $session->set(self::SESSION_REFERER, null);
    }

}