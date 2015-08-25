<?php

namespace joppa\contact\controller;

use joppa\contact\form\ContactWidgetForm;
use joppa\contact\form\ContactWidgetPropertiesForm;
use joppa\contact\view\ContactWidgetView;
use joppa\contact\view\ContactWidgetPropertiesView;

use zibo\library\html\form\captcha\exception\CaptchaException;
use zibo\library\html\form\captcha\CaptchaManager;
use zibo\library\mail\Message;
use zibo\library\validation\exception\ValidationException;
use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to send a message through email
 */
class ContactWidget extends AbstractWidget {

	/**
	 * Path to the icon of this message
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/contact.png';

	/**
	 * Name of the recipient property
	 * @var string
	 */
    const PROPERTY_RECIPIENT = 'recipient';

    /**
	 * Name of the subject property
	 * @var string
	 */
    const PROPERTY_SUBJECT = 'subject';

    /**
     * Translation key for the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.contact.widget.name';

    /**
     * Translation key for the message when a message is sent
     * @var string
     */
    const TRANSLATION_MESSAGE_SENT = 'joppa.contact.message.sent';

    /**
     * Translation key for the warning when the recipient is not set
     * @var string
     */
    const TRANSLATION_WARNING_RECIPIENT = 'joppa.contact.warning.recipient';

    /**
     * Constructs a new contact widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to show and process the contact form
     * @return null
     */
    public function indexAction() {
        $recipient = $this->properties->getWidgetProperty(self::PROPERTY_RECIPIENT);
        if (!$recipient) {
        	$this->addWarning(self::TRANSLATION_WARNING_RECIPIENT);
        	return;
        }

    	$captchaManager = CaptchaManager::getInstance();

        $form = new ContactWidgetForm($this->request->getBasePath());
        $captchaManager->addCaptchaToForm($form);

        if ($form->isSubmitted()) {
            try {
                $form->validate();
                $captchaManager->validateCaptcha($form);

                $name = $form->getName();
                $email = $form->getEmail();
                $message = $form->getMessage();
		        $subject = $this->properties->getWidgetProperty(self::PROPERTY_SUBJECT);

                $mail = new Message();
                $mail->setFrom($name . ' <' . $email . '>');
                $mail->setTo($recipient);
                $mail->setSubject($subject);
                $mail->setMessage($message);
                $mail->send();

                $this->addInformation(self::TRANSLATION_MESSAGE_SENT);
                $this->response->setRedirect($this->request->getBasePath());
                return;
            } catch (ValidationException $e) {

            } catch (CaptchaException $e) {

            }
        }

        $captchaView = $captchaManager->getCaptchaView($form);

        $view = new ContactWidgetView($form, $captchaView);
        $this->response->setView($view);
    }

    /**
     * Gets a preview of the properties
     * @return string
     */
    public function getPropertiesPreview() {
        return $this->properties->getWidgetProperty(self::PROPERTY_RECIPIENT);
    }

    /**
     * Action to manage the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $recipient = $this->properties->getWidgetProperty(self::PROPERTY_RECIPIENT);
        $subject = $this->properties->getWidgetProperty(self::PROPERTY_SUBJECT);

        $form = new ContactWidgetPropertiesForm($this->request->getBasePath(), $recipient, $subject);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $form->validate();

                $recipient = $form->getRecipient();
                $subject = $form->getSubject();

                $this->properties->setWidgetProperty(self::PROPERTY_RECIPIENT, $recipient);
                $this->properties->setWidgetProperty(self::PROPERTY_SUBJECT, $subject);

                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {

            }
        }

        $view = new ContactWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

}