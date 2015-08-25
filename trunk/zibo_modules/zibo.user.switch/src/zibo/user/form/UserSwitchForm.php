<?php

namespace zibo\user\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to switch the current user
 */
class UserSwitchForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formUserSwitch';

    /**
     * Name for the username field
     * @var string
     */
    const FIELD_USERNAME = 'username';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'user.button.switch';

    /**
     * Constructs a new authentication form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

        $factory = FieldFactory::getInstance();

        $fieldUsername = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_USERNAME);
        $fieldUsername->addValidator(new RequiredValidator());

        $this->addField($fieldUsername);
    }

    /**
     * Gets the username submitted with this form
     * @return string
     */
    public function getUsername() {
        return $this->getValue(self::FIELD_USERNAME);
    }

    /**
     * Enables autocompletion on the username field
     * @param string|array $source The source for the autocompletion
     * @param integer $minLength The minimum length of the input before performing the autocompletion
     * @return null
     */
    public function setAutoComplete($source, $minLength = null) {
        $fieldUsername = $this->getField(self::FIELD_USERNAME);
        $fieldUsername->setAutoComplete($source, $minLength);
    }

}