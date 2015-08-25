<?php

namespace joppa\security\controller;

use joppa\model\NodeModel;

use joppa\security\form\NewPasswordForm;
use joppa\security\form\PasswordWidgetPropertiesForm;
use joppa\security\form\ResetPasswordForm;
use joppa\security\view\NewPasswordView;
use joppa\security\view\PasswordWidgetPropertiesView;
use joppa\security\view\ResetPasswordView;

use zibo\library\mail\Message;
use zibo\library\security\model\User;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;
use zibo\library\String;

/**
 * Widget to reset a users password
 */
class PasswordWidget extends AbstractSecurityWidget {

	/**
	 * Action to reset the password
	 * @var string
	 */
	const ACTION_RESET = 'reset';

	/**
	 * Path to the icon of this widget
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/password.png';

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.security.widget.password.name';

    /**
     * Translation key for the error when a user has no email address set
     * @var string
     */
    const TRANSLATION_ERROR_USER_NO_EMAIL = 'joppa.security.error.user.no.email';

    /**
     * Translation key for the information message when a mail has been sent
     * @var string
     */
    const TRANSLATION_INFORMATION_SEND_MAIL = 'joppa.security.information.mail.sent.password';

    /**
     * Constructs a new password reset widget
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Gets the names of the possible request parameters of this widget
     * @return array
     */
    public function getRequestParameters() {
    	return array(self::ACTION_RESET);
    }

    /**
     * Action to ask for the user and send a mail with the password reset URL
     * @return null
     */
    public function indexAction() {
    	$basePath = $this->request->getBasePath();

    	$form = new ResetPasswordForm($basePath);
    	if (!$form->isSubmitted()) {
    		// form not submitted, show the view
    		$this->setResetPasswordView($form);
    		return;
    	}

    	if ($form->isCancelled()) {
    		// not implemented
    		$this->response->setRedirect($basePath);
    		return;
    	}

    	try {
    		// validates the form
    		$form->validate();

    		// initialize needed variables
    		$securityModel = SecurityManager::getInstance()->getSecurityModel();

    		$username = $form->getUsername();
    		$email = $form->getEmail();

    		$error = null;
    		$errorField = null;

    		// gets the user by the provided field
    		if ($username) {
    			$errorField = ResetPasswordForm::FIELD_USERNAME;

	    		$user = $securityModel->getUserByUsername($username);
	    		if (!$user) {
	    			$error = new ValidationError(self::TRANSLATION_ERROR_USER_NOT_FOUND_USERNAME, 'Could not find the profile for username %username%', array('username' => $username));
	    		}
    		} else {
    			$errorField = ResetPasswordForm::FIELD_EMAIL;

	    		$user = $securityModel->getUserByEmail($email);
	    		if (!$user) {
	    			$error = new ValidationError(self::TRANSLATION_ERROR_USER_NOT_FOUND_EMAIL, 'Could not find the profile for email address %email%', array('email' => $email));
	    		}
    		}

    		if ($error) {
	    		// validation errors occured
                $exception = new ValidationException();
                $exception->addErrors($errorField, array($error));

                throw $exception;
    		}

    		$email = $user->getUserEmail();
    		if (!$email) {
	    		// no email set for the user
    			$error = new ValidationError(self::TRANSLATION_ERROR_USER_NO_EMAIL, 'No email address set for your profile');

                $exception = new ValidationException();
                $exception->addErrors($errorField, array($error));

                throw $exception;
    		}

    		// generates a secure key and the reset URL
    		$key = $this->getUserKey($user);
    		$resetUrl = $basePath . '/' . self::ACTION_RESET . '/' . $email . '/' . $key;

    		// send the reset URL to the user
    		$this->sendMail($user, $resetUrl);

    		// show information message and redirect the page
    		$this->addInformation(self::TRANSLATION_INFORMATION_SEND_MAIL);
    		$this->response->setRedirect($basePath);
            return;
    	} catch (ValidationException $exception) {
    		$form->setValidationException($exception);
    	}

    	// a error occured, show the form with the errors
    	$this->setResetPasswordView($form);
    }

    /**
     * Sets the password view to the response
     * @param joppa\security\form\ResetPasswordForm $form Form of the password view
     * @return null
     */
    private function setResetPasswordView(ResetPasswordForm $form) {
    	$view = new ResetPasswordView($form);
    	$this->response->setView($view);
    }

    /**
     * Action to reset the password
     * @param string $email The email address of the user
     * @param string $key The secure key of the user
     * @return null
     */
    public function resetAction($email = null, $key = null) {
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

        $formAction = $basePath . '/' . self::ACTION_RESET . '/' . $email . '/' . $key;
        $form = new NewPasswordForm($formAction);
        if ($form->isSubmitted()) {
        	try {
        		$form->validate();

        		$password = $form->getPassword();

	            $user->setUserPassword($password);

	            $securityModel->setUser($user);

	            $this->setRedirectToLogin();
	            return;
        	} catch (ValidationException $exception) {

        	}
        }

        $view = new NewPasswordView($form);
        $this->response->setView($view);
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

    	$form = new PasswordWidgetPropertiesForm($this->request->getBasePath(), $subject, $message);
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

    	$view = new PasswordWidgetPropertiesView($form);
    	$this->response->setView($view);

    	return false;
    }

}