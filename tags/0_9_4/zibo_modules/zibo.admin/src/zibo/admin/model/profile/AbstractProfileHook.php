<?php

namespace zibo\admin\model\profile;

use zibo\admin\controller\AbstractController;
use zibo\admin\form\ProfileForm;

use zibo\library\security\model\User;
use zibo\library\security\SecurityManager;

/**
 * Abstract profile hook
 */
abstract class AbstractProfileHook implements ProfileHook {

    /**
     * The profile form
     * @var ProfileForm
     */
    protected $profileForm;

    /**
     * The user
     * @var zibo\library\security\model\User
     */
    protected $user;

    /**
     * Sets the profile form
     * @param ProfileForm $form The profile form
     * @return null
     */
    public function setProfileForm(ProfileForm $form) {
        $this->profileForm = $form;
        $this->user = $form->getUser();
    }

    /**
     * Gets the profile form
     * @return ProfileForm The profile form
     */
    public function getProfileForm() {
        return $this->profileForm;
    }

    /**
     * Provides a hook to initialize the profile form, add fields ...
     * @return null
     */
    public function onProfileFormInitialize() {

    }

    /**
     * Provides a hook for additional profile form validation
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when a validation error occurs
     */
    public function onProfileFormValidate() {

    }

    /**
     * Provides a hook to save the submitted profile
     * @param zibo\admin\controller\AbstractController $controller The controller of the request
     * @return null
     */
    public function onProfileFormSubmit(AbstractController $controller) {

    }

    /**
     * Gets the view of this hook for the profile form
     * @return zibo\core\view\HtmlView
     */
    public function getProfileFormView() {
        return null;
    }

    /**
     * Saves the user
     * @param zibo\library\security\model\User
     * @return null
     */
    protected function saveUser(User $user) {
        SecurityManager::getInstance()->setUser($user);
    }

}