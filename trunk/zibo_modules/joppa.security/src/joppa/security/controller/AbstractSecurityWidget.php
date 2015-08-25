<?php

namespace joppa\security\controller;

use joppa\controller\JoppaWidget;

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
 * Abstract widget of the joppa security module
 */
abstract class AbstractSecurityWidget extends JoppaWidget {

    /**
     * Translation key for the subject label
     * @var string
     */
    const TRANSLATION_SUBJECT = 'joppa.security.label.subject';

    /**
     * Translation key for the user not found error by username
     * @var string
     */
    const TRANSLATION_ERROR_USER_NOT_FOUND_USERNAME = 'joppa.security.error.user.not.found.username';

    /**
     * Translation key for the user not found error by email address
     * @var string
     */
    const TRANSLATION_ERROR_USER_NOT_FOUND_EMAIL = 'joppa.security.error.user.not.found.email';

    /**
     * Translation key for the error when the provided secure key is incorrect
     * @var string
     */
    const TRANSLATION_ERROR_INCORRECT_KEY = 'joppa.security.error.key.incorrect';

    /**
     * Name of the subject property
     * @var string
     */
    const PROPERTY_SUBJECT = 'subject';

    /**
     * Name of the message property
     * @var string
     */
    const PROPERTY_MESSAGE = 'message';

    /**
     * Hook with the ORM module
     * @var string
     */
    public $useModels = NodeModel::NAME;

    /**
     * Sends a mail to the provided user
     * @param zibo\library\security\model\User $user The user
     * @param string $url The URL to be parsed in the message
     * @return null
     */
    protected function sendMail(User $user, $url) {
    	$subject = $this->getSubject();
    	$message = $this->getMessage();

    	$username = $user->getUserName();
    	$email = $user->getUserEmail();

    	$message = str_replace('%username%', $username, $message);
    	$message = str_replace('%email%', $email, $message);

    	if ($url) {
    	   $message = str_replace('%url%', $url, $message);
    	}

    	$mail = new Message();
    	$mail->setTo($email);
    	$mail->setSubject($subject);
    	$mail->setMessage($message);

    	$mail->send();
    }

    /**
     * Generates a secure key for the provided user
     * @param zibo\library\security\model\User $user The user to get the key of
     * @return string The secure key for the provided user
     */
    protected function getUserKey(User $user) {
    	$key = $user->getUserId();
    	$key .= '-' . $user->getUserName();
    	$key .= '-' . $user->getUserEmail();

    	return md5($key);
    }

    /**
     * Redirects the response to the login page
     * @param string $default Fall back URL if there is no node with the login widget
     * @return null
     */
    protected function setRedirectToLogin($default = null) {
    	if (!$default) {
    		$default = $this->request->getBasePath();
    	}

        $loginNode = $this->models[NodeModel::NAME]->getNodesForWidget('joppa', 'login', 1);
        if ($loginNode) {
            $this->response->setRedirect($this->request->getBaseUrl() . '/' . $loginNode->getRoute());
        } else {
            $this->response->setRedirect($default);
        }
    }

    /**
     * Gets the subject of the mail from the properties
     * @return string The subject of the mail
     */
    protected function getSubject() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_SUBJECT . '.' . $this->locale);
    }

    /**
     * Sets the subject of the mail to the properties
     * @param string $subject The subject of the mail
     * @return null
     */
    protected function setSubject($subject) {
    	$this->properties->setWidgetProperty(self::PROPERTY_SUBJECT. '.' . $this->locale, $subject);
    }

    /**
     * Gets the message of the mail from the properties
     * @return string The message of the mail
     */
    protected function getMessage() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_MESSAGE. '.' . $this->locale);
    }

    /**
     * Sets the message of the mail to the properties
     * @param string $message The message of the mail
     * @return null
     */
    protected function setMessage($message) {
    	$this->properties->setWidgetProperty(self::PROPERTY_MESSAGE. '.' . $this->locale, $message);
    }

}