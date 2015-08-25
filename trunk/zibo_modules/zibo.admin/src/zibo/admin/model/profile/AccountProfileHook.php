<?php

namespace zibo\admin\model\profile;

use zibo\admin\controller\AbstractController;
use zibo\admin\view\security\AccountProfileView;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\validation\ValidationError;

/**
 * Profile form hook for the user account
 */
class AccountProfileHook extends AbstractProfileHook {

    /**
     * Name of the email field
     * @var string
     */
    const FIELD_EMAIL = 'email';

    /**
     * Name of the password field
     * @var string
     */
    const FIELD_PASSWORD = 'newPassword';

    /**
     * Name of the password confirm field
     * @var string
     */
    const FIELD_PASSWORD_CONFIRM = 'newPasswordConfirm';

    /**
     * Name of the update email button
     * @var string
     */
    const BUTTON_UPDATE_EMAIL = 'submitEmail';

    /**
     * Name of the update password button
     * @var string
     */
    const BUTTON_UPDATE_PASSWORD = 'submitPassword';

    /**
     * Translation key for the update buttons
     * @var string
     */
    const TRANSLATION_UPDATE = 'security.button.update';

    /**
     * Translation key for the error message when the submitted passwords don't match each other
     * @var unknown_type
     */
    const TRANSLATION_ERROR_PASSWORD_MATCH = 'security.error.password.match';

    /**
     * Translation key for the information message when the profile is successfully saved
     * @var string
     */
    const TRANSLATION_SAVED = 'security.message.profile.saved';

    /**
     * Provides a hook to initialize the form, add fields ...
     * @return null
     */
    public function onProfileFormInitialize() {
        $fieldFactory = FieldFactory::getInstance();

        $emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL, $this->user->getUserEmail());
        $updateEmailButton = $fieldFactory->createSubmitField(self::BUTTON_UPDATE_EMAIL, self::TRANSLATION_UPDATE);

        $passwordField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD);
        $passwordConfirmField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD_CONFIRM);
        $updatePasswordButton = $fieldFactory->createSubmitField(self::BUTTON_UPDATE_PASSWORD, self::TRANSLATION_UPDATE);

        $this->profileForm->addField($emailField);
        $this->profileForm->addField($updateEmailButton);
        $this->profileForm->addField($passwordField);
        $this->profileForm->addField($passwordConfirmField);
        $this->profileForm->addField($updatePasswordButton);
    }

    /**
     * Provides a hook for additional form validation
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when a validation error occurs
     */
    public function onProfileFormValidate() {
        $validationException = new ValidationException();

        if ($this->isPasswordSubmitted()) {
            $password = $this->getPassword();
            $passwordConfirmation = $this->getPasswordConfirmation();

            $validator = new RequiredValidator();
            if (!$validator->isValid($password)) {
                $validationException->addErrors(self::FIELD_PASSWORD, $validator->getErrors());
            }
            if (!$validator->isValid($passwordConfirmation)) {
                $validationException->addErrors(self::FIELD_PASSWORD_CONFIRM, $validator->getErrors());
            }

            if (!$validationException->hasErrors() && $password != $passwordConfirmation) {
                $error = new ValidationError(self::TRANSLATION_ERROR_PASSWORD_MATCH, "Your passwords do not match");
                $validationException->addErrors(self::FIELD_PASSWORD, array($error));
            }
        }

        if ($validationException->hasErrors()) {
            throw $validationException;
        }
    }

    /**
     * Provides a hook to save the submitted profile
     * @param zibo\admin\controller\AbstractController $controller The controller of the request
     * @return null
     */
    public function onProfileFormSubmit(AbstractController $controller) {
        if ($this->isEmailSubmitted()) {
            $email = $this->getEmail();

            $this->user->setUserEmail($email);

            $this->saveUser($this->user);
            $controller->addInformation(self::TRANSLATION_SAVED);
        }

        if ($this->isPasswordSubmitted()) {
            $password = $this->getPassword();
            $passwordConfirmation = $this->getPasswordConfirmation();

            $this->user->setUserPassword($password);

            $this->saveUser($this->user);
            $controller->addInformation(self::TRANSLATION_SAVED);
        }
    }

    /**
     * Gets the view for the profile form
     * @return zibo\core\view\HtmlView
     */
    public function getProfileFormView() {
        return new AccountProfileView($this->profileForm);
    }

    /**
     * Gets whether the email has been submitted
     * @return boolean
     */
    private function isEmailSubmitted() {
        return $this->profileForm->getValue(self::BUTTON_UPDATE_EMAIL) ? true : false;
    }

    /**
     * Gets whether the password has been submitted
     * @return boolean
     */
    private function isPasswordSubmitted() {
        return $this->profileForm->getValue(self::BUTTON_UPDATE_PASSWORD) ? true : false;
    }

    /**
     * Gets the submitted email address
     * @return string
     */
    private function getEmail() {
        return $this->profileForm->getValue(self::FIELD_EMAIL);
    }

    /**
     * Gets the password submitted with this form
     * @return string
     */
    private function getPassword() {
        return $this->profileForm->getValue(self::FIELD_PASSWORD);
    }

    /**
     * Gets the password confirmation submitted with this form
     * @return string
     */
    private function getPasswordConfirmation() {
        return $this->profileForm->getValue(self::FIELD_PASSWORD_CONFIRM);
    }

}