<?php

namespace joppa\security\controller;

use joppa\model\NodeModel;

use joppa\security\form\RegisterWidgetPropertiesForm;
use joppa\security\form\RegisterForm;
use joppa\security\view\RegisterView;
use joppa\security\view\RegisterWidgetPropertiesView;

use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;

/**
 * Widget to register a new user
 */
class RegisterWidget extends AbstractSecurityWidget {

    /**
     * Action to activate the user
     * @var string
     */
    const ACTION_ACTIVATE = 'activate';

	/**
	 * Path to the icon of the widget
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/register.png';

	/**
	 * Translation key for the name of the widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.security.widget.register.name';

    /**
     * Translation key for the information message when a mail has been sent
     * @var string
     */
    const TRANSLATION_INFORMATION_SEND_MAIL = 'joppa.security.information.mail.sent.register';

    /**
     * Hook with the ORM module
     * @var string
     */
    public $useModels = NodeModel::NAME;

    /**
     * Constructs a new logout widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Gets the names of the possible request parameters of this widget
     * @return array
     */
    public function getRequestParameters() {
        return array(self::ACTION_ACTIVATE);
    }

    /**
     * Action to register a new user
     * @return null
     */
    public function indexAction() {
    	$basePath = $this->request->getBasePath();

        $form = new RegisterForm($basePath);
        if (!$form->isSubmitted()) {
        	$this->setRegisterView($form);
        	return;
        }

        $securityModel = SecurityManager::getInstance()->getSecurityModel();

        try {
        	$form->validate();

        	$username = $form->getUsername();
        	$email = $form->getEmail();
        	$password = $form->getPassword();

	        $user = $securityModel->createUser();

	        $user->setUserName($username);
	        $user->setUserEmail($email);
	        $user->setUserPassword($password);
	        $user->setIsUserActive(false);

	        $securityModel->setUser($user);

            // generates a secure key and the reset URL
            $key = $this->getUserKey($user);
            $activateUrl = $basePath . '/' . self::ACTION_ACTIVATE . '/' . $email . '/' . $key;

	        $this->sendMail($user, $activateUrl);

	        $this->addInformation(self::TRANSLATION_INFORMATION_SEND_MAIL);
            $this->setRedirectToLogin();
            return;
        } catch (ValidationException $exception) {
        	$form->setValidationException($exception);
        }

        $this->setRegisterView($form);
    }

    /**
     * Sets the register view to the response
     * @param joppa\security\form\RegisterForm $form Form of the register view
     * @return null
     */
    private function setRegisterView(RegisterForm $form) {
        $view = new RegisterView($form);
        $this->response->setView($view);
    }

    /**
     * Action to activate the user
     * @param string $email The email address of the user
     * @param string $key The secure key of the user
     * @return null
     */
    public function activateAction($email = null, $key = null) {
        $basePath = $this->request->getBasePath();

        if (!$email || !$key) {
            $this->response->setRedirect($basePath);
            return;
        }

        $securityModel = SecurityManager::getInstance()->getSecurityModel();

        $user = $securityModel->getUserByEmail($email);
        if (!$user) {
            $this->addError(self::TRANSLATION_ERROR_USER_NOT_FOUND_EMAIL, array('email' => $email));
            $this->response->setRedirect($basePath);
            return;
        }

        $userKey = $this->getUserKey($user);
        if ($userKey != $key) {
            $this->addError(self::TRANSLATION_ERROR_INCORRECT_KEY);
            $this->response->setRedirect($basePath);
            return;
        }

        try {
            $user->setIsUserActive(true);

            $securityModel->setUser($user);

            $this->setRedirectToLogin();
            return;
        } catch (ValidationException $exception) {

        }

        $this->response->setRedirect($basePath);
    }

    /**
     * Gets a preview of the current properties
     * @return string
     */
    public function getPropertiesPreview() {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        if (!$subject || !$message) {
            return '---';
        }

        $translator = $this->getTranslator();

        return $translator->translate(self::TRANSLATION_SUBJECT) . ': ' . $subject;
    }

    /**
     * Action to edit the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        $form = new RegisterWidgetPropertiesForm($this->request->getBasePath(), $subject, $message);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $form->validate();

                $subject = $form->getSubject();
                $message = $form->getMessage();

                $this->setSubject($subject);
                $this->setMessage($message);

                $this->addInformation(self::TRANSLATION_PROPERTIES_SAVED);

                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $exception) {

            }
        }

        $view = new RegisterWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }


}