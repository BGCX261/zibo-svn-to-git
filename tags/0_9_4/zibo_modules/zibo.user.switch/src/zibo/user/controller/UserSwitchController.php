<?php

namespace zibo\user\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\view\JsonView;
use zibo\core\Zibo;

use zibo\library\security\exception\UserNotFoundException;
use zibo\library\security\exception\UserSwitchException;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

use zibo\user\form\UserSwitchForm;
use zibo\user\view\UserSwitchView;

/**
 * Controller to switch between users
 */
class UserSwitchController extends AbstractController {

    /**
     * Name of the auto complete action for the username
     * @var string
     */
    const ACTION_AUTO_COMPLETE_USER = 'autocomplete';

    /**
     * Translation key for the user not found error
     * @var string
     */
    const TRANSLATION_ERROR_USER_NOT_FOUND = 'user.error.not.found';

    /**
     * Translation key for the user not allowed error
     * @var string
     */
    const TRANSLATION_ERROR_USER_NOT_ALLOWED = 'user.error.not.allowed';

    /**
     * Instance of the security manager
     * @var zibo\library\security\SecurityManager
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
     * Action to switch the current user
     * @param string $username Username of the user to switch to
     * @return null
     */
    public function indexAction($username = null) {
        if ($username) {
            $this->securityManager->switchUser($username);

            $this->response->setRedirect($this->getReferer());
            return;
        }

        $basePath = $this->request->getBasePath();

        $form = new UserSwitchForm($basePath);
        $form->setAutoComplete($basePath . '/' . self::ACTION_AUTO_COMPLETE_USER, 2);
        if (!$form->isSubmitted()) {
            $this->setUserSwitchView($form);
            return;
        }

        if ($form->isCancelled()) {
            $this->response->setRedirect($this->request->getBaseUrl());
            return;
        }

        try {
            $form->validate();

            $username = $form->getUsername();

            $this->securityManager->switchUser($username);

            $this->response->setRedirect($this->request->getBaseUrl());
            return;
        } catch (UserNotFoundException $userNotFoundException) {
            $validationError = new ValidationError(self::TRANSLATION_ERROR_USER_NOT_FOUND, 'Could not find user %user%', array('user' => $username));

            $validationException = new ValidationException();
            $validationException->addErrors(UserSwitchForm::FIELD_USERNAME, array($validationError));

            $form->setValidationException($validationException);
        } catch (UserSwitchException $userSwitchException) {
            $validationError = new ValidationError(self::TRANSLATION_ERROR_USER_NOT_ALLOWED, 'You are not allowed to switch to %user%', array('user' => $username));

            $validationException = new ValidationException();
            $validationException->addErrors(UserSwitchForm::FIELD_USERNAME, array($validationError));

            $form->setValidationException($validationException);
        } catch (ValidationException $validationException) {

        }

        $this->setUserSwitchView($form);
    }

    /**
     * Action to auto complete a username
     * @return null
     */
    public function autocompleteAction() {
        $environment = $this->getEnvironment();

        $term = $environment->getArgument('term');
        if (!$term) {
            return;
        }

        $securityModel = $this->securityManager->getSecurityModel();
        $users = $securityModel->findUsersByUsername($term);

        $view = new JsonView($users);
        $this->response->setView($view);
    }

    /**
     * Sets the switch user view to the response
     * @param zibo\user\form\UserSwitchForm $form Switch user form for the view
     * @return null
     */
    private function setUserSwitchView(UserSwitchForm $form) {
        $view = new UserSwitchView($form);
        $this->response->setView($view);
    }

}