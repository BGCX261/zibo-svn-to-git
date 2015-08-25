<?php

namespace zibo\admin\form;

use zibo\admin\controller\AbstractController;
use zibo\admin\model\profile\ProfileHook;

use zibo\library\html\form\Form;
use zibo\library\security\model\User;
use zibo\library\validation\exception\ValidationException;

/**
 * Form of a user profile
 */
class ProfileForm extends Form {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formProfile';

    /**
     * The user of the form
     * @var zibo\library\security\model\User
     */
    private $user;

    /**
     * The hooks of the form
     * @var array
     */
    private $hooks;

    /**
     * Constructs a new profile form
     * @param string $action URL where this form will point to
     * @param string User The user of the profile
     * @return null
     */
    public function __construct($action, User $user) {
        parent::__construct($action, self::NAME);

        $this->user = $user;
        $this->hooks = array();
    }

    /**
     * Gets the user of the form
     * @return zibo\library\security\model\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Adds a profile hook to the form
     * @param ProfileHook $hook The hook to add
     * @return null
     */
    public function addHook(ProfileHook $hook) {
        $this->hooks[] = $hook;

        $hook->setProfileForm($this);
        $hook->onProfileFormInitialize();
    }

    /**
     * Gets the views of the hooks
     * @return array Array with View objects
     */
    public function getHookViews() {
        $views = array();

        foreach ($this->hooks as $hook) {
            $view = $hook->getProfileFormView();
            if (!$view) {
                continue;
            }

            $views[] = $view;
        }

        return $views;
    }

    /**
     * Validates this form
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when one of the fields or the form itself is not validated
     */
    public function validate() {
        try {
            parent::validate();
            $validationException = new ValidationException();
        } catch (ValidationException $exception) {
            $validationException = $exception;
        }

        foreach ($this->hooks as $hook) {
            try {
                $hook->onProfileFormValidate();
            } catch (ValidationException $exception) {
                $allErrors = $exception->getAllErrors();
                foreach ($allErrors as $field => $errors) {
                    $validationException->addErrors($field, $errors);
                }
            }
        }

        if ($validationException->hasErrors()) {
            throw $validationException;
        }
    }

    /**
     * Processes the submission for the hooks
     * @param zibo\admin\controller\AbstractController $controller The controller of the request
     * @return null
     */
    public function processSubmit(AbstractController $controller) {
        foreach ($this->hooks as $hook) {
            $hook->onProfileFormSubmit($controller);
        }
    }

}