<?php

namespace zibo\admin\model\profile;

use zibo\admin\controller\AbstractController;
use zibo\admin\form\ProfileForm;

/**
 * Interface for the profile form hook
 */
interface ProfileHook {

    /**
     * Sets the profile form
     * @param ProfileForm $form The profile form
     * @return null
     */
    public function setProfileForm(ProfileForm $form);

    /**
     * Gets the profile form
     * @return ProfileForm The profile form
     */
    public function getProfileForm();

    /**
     * Provides a hook to initialize the profile form, add fields ...
     * @return null
     */
    public function onProfileFormInitialize();

    /**
     * Provides a hook for additional profile form validation
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when a validation error occurs
     */
    public function onProfileFormValidate();

    /**
     * Provides a hook to save the submitted profile
     * @param zibo\admin\controller\AbstractController $controller The controller of the request
     * @return null
     */
    public function onProfileFormSubmit(AbstractController $controller);

    /**
     * Gets the view of this hook for the profile form
     * @return zibo\core\view\HtmlView
     */
    public function getProfileFormView();

}