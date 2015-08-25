<?php

namespace zibo\admin\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\security\SecurityManager;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to authenticate a user through username and password
 */
class AuthenticationForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formAuthenticate';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_LOGIN = 'security.button.login';

    /**
     * Constructs a new authentication form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME, self::TRANSLATION_LOGIN);

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_STRING, SecurityManager::USERNAME));
        $this->addField($factory->createField(FieldFactory::TYPE_PASSWORD, SecurityManager::PASSWORD));

        $requiredValidator = new RequiredValidator();

        $this->addValidator(SecurityManager::USERNAME, $requiredValidator);
        $this->addValidator(SecurityManager::PASSWORD, $requiredValidator);
    }

    /**
     * Gets the username submitted with this form
     * @return string
     */
    public function getUsername() {
        return $this->getValue(SecurityManager::USERNAME);
    }

    /**
     * Gets the password submitted with this form
     * @return string
     */
    public function getPassword() {
        return $this->getValue(SecurityManager::PASSWORD);
    }

}