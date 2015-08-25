<?php

namespace zibo\install\form;

use zibo\install\view\InstallStepProfileView;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\wizard\step\AbstractWizardStep;

/**
 * Step 1 of the Zibo installation: profile select
 */
class InstallStepProfile extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepProfile';

    /**
     * Name of the profile field
     * @var string
     */
    const FIELD_PROFILE = 'profile';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new InstallStepProfileView($this->wizard);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $fieldFactory = FieldFactory::getInstance();
        $translator = I18n::getInstance()->getTranslator();

        $profileList = array();
        $profiles = $this->wizard->getInstaller()->getProfiles();
        foreach ($profiles as $profileName => $profile) {
            $profileList[$profileName] = $profile->getName($translator) . '<span>' . $profile->getDescription($translator) . '</span>';
        }

        $profile = $this->wizard->getProfile();

        $profileField = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_PROFILE, $profile);
        $profileField->setOptions($profileList);
        $profileField->addValidator(new RequiredValidator());

        $this->wizard->addField($profileField);
    }

    /**
     * Processes the next action of this step
     * return string Name of the next step
     */
    public function next() {
        try {
            $this->wizard->validate();
        } catch (ValidationException $validationException) {
            return null;
        }

        $profile = $this->wizard->getValue(self::FIELD_PROFILE);

        $this->wizard->setProfile($profile);

        return InstallStepRequirement::NAME;
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return false;
    }

}