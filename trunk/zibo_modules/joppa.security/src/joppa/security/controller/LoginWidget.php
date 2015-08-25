<?php

namespace joppa\security\controller;

use joppa\security\form\LoginWidgetPropertiesForm;
use joppa\security\view\LoginWidgetPropertiesView;
use joppa\security\view\LoginWidgetView;

use zibo\admin\Module as AdminModule;
use zibo\admin\form\AuthenticationForm;

use zibo\library\i18n\I18n;
use zibo\library\security\exception\AuthenticationException;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;
use zibo\library\widget\controller\AbstractWidget;

class LoginWidget extends AbstractWidget {

	/**
	 * Path to the icon of this widget
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/login.png';

	/**
	 * Session key for the referer
	 * @var string
	 */
	const SESSION_REFERER = 'joppa.login.referer';

	/**
	 * Name of the redirect property
	 * @var string
	 */
	const PROPERTY_REDIRECT = 'redirect';

	/**
	 * No redirect value
	 * @var string
	 */
	const REDIRECT_NO = 'no';

	/**
	 * Redirect to the home page
	 * @var string
	 */
	const REDIRECT_HOME = 'home';

	/**
	 * Redirect to the referer
	 * @var string
	 */
	const REDIRECT_REFERER = 'referer';

	/**
	 * Translation key for the name of the widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.security.widget.login.name';

    /**
     * Translation key for the redirect label
     * @var string
     */
    const TRANSLATION_REDIRECT = 'joppa.security.label.redirect';

    /**
     * Translation key for the no redirect option
     * @var string
     */
    const TRANSLATION_REDIRECT_NO = 'joppa.security.option.redirect.no';

    /**
     * Translation key for the home redirect option
     * @var string
     */
    const TRANSLATION_REDIRECT_HOME = 'joppa.security.option.redirect.home';

    /**
     * Translation key for the referer redirect option
     * @var string
     */
    const TRANSLATION_REDIRECT_REFERER = 'joppa.security.option.redirect.referer';

    /**
     * Construct a new login widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to show the authentication form and to process authentication
     * @return null
     */
    public function indexAction() {
        $securityManager = SecurityManager::getInstance();
        $session = $this->getSession();
    	$redirect = $this->getRedirect();

        $user = $securityManager->getUser();
        if ($user) {
        	// user is already logged in
        	$redirectUrl = null;

        	switch ($redirect) {
        		case self::REDIRECT_HOME:
        			$redirectUrl = $this->request->getBaseUrl();
        			break;
        		case self::REDIRECT_REFERER:
	        		$redirectUrl = $this->getReferer();
        			break;
        	}

        	if ($redirectUrl) {
            	$this->response->setRedirect($redirectUrl);
        	}

            return;
        }

        // gets the general referer
        $referer = $session->get(AdminModule::SESSION_REFERER);
        if (!$referer || substr_compare($referer, $this->request->getBasePath(), 0, strlen($this->request->getBasePath())) == 1) {
            $referer = $this->request->getBaseUrl();
        }

        $form = new AuthenticationForm($this->request->getBasePath());
        if (!$form->isSubmitted()) {
        	// the form is not submitted, store the general referer as the login referer
            $session->set(self::SESSION_REFERER, $referer);
            $this->setLoginView($form);
            return;
        }

        // gets the login referer
        $redirectUrl = $session->get(self::SESSION_REFERER, $referer);

        if ($form->isCancelled()) {
        	// the form is cancelled, redirect to the login referer
            $this->response->setRedirect($redirectUrl);
            return;
        }

        try {
        	// try to authenticate the user
            $form->validate();

            $username = $form->getValue(SecurityManager::USERNAME);
            $password = $form->getValue(SecurityManager::PASSWORD);

            $securityManager->login($username, $password);

            // get the redirect url
            $redirect = $this->getRedirect();
            switch ($redirect) {
            	case self::REDIRECT_NO:
            		$redirectUrl = $this->request->getBasePath();
            		break;
            	case self::REDIRECT_HOME:
            		$redirectUrl = $this->request->getBaseUrl();
            		break;
            }

            $this->response->setRedirect($redirectUrl);
            return;
        } catch (AuthenticationException $e) {
        	// authentication error
            if ($e->getField() == null) {
                throw $e;
            }

            $error = new ValidationError($e->getTranslationKey(), $e->getMessage());
            $exception = new ValidationException();
            $exception->addErrors($e->getField(), array($error));

            $form->setValidationException($exception);
        } catch (ValidationException $exception) {
            // no username or password filled in, exception already set to the form
        }

        $this->setLoginView($form);
    }

    /**
     * Sets the login view to the response
     * @param zibo\admin\AuthenticationForm $form
     * @return null
     */
    private function setLoginView(AuthenticationForm $form) {
        $view = new LoginWidgetView($form);
        $this->response->setView($view);
    }

    /**
     * Gets a preview of the current properties
     * @return string
     */
    public function getPropertiesPreview() {
    	$translator = $this->getTranslator();

    	$preview = $translator->translate(self::TRANSLATION_REDIRECT) . ': ';

    	$redirectOptions = self::getRedirectOptions();

    	$redirect = $this->getRedirect();
    	if (array_key_exists($redirect, $redirectOptions)) {
    		$preview .= $redirectOptions[$redirect];
    	} else {
    		$preview .= '---';
    	}

    	return $preview;
    }

    /**
     * Action to edit the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $redirect = $this->getRedirect();

        $form = new LoginWidgetPropertiesForm($this->request->getBasePath(), $redirect);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $redirect = $form->getRedirect();

                $this->properties->setWidgetProperty(self::PROPERTY_REDIRECT, $redirect);
                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new LoginWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the redirect property
     * @return string
     */
    private function getRedirect() {
        return $this->properties->getWidgetProperty(self::PROPERTY_REDIRECT, self::REDIRECT_HOME);
    }

    /**
     * Gets the available redirect options
     * @return array
     */
    public static function getRedirectOptions() {
        $translator = I18n::getInstance()->getTranslator();

        $options = array(
            self::REDIRECT_NO => $translator->translate(self::TRANSLATION_REDIRECT_NO),
            self::REDIRECT_HOME => $translator->translate(self::TRANSLATION_REDIRECT_HOME),
            self::REDIRECT_REFERER => $translator->translate(self::TRANSLATION_REDIRECT_REFERER),
        );

        return $options;
    }

}