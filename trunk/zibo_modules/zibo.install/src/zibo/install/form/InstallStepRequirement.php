<?php

namespace zibo\install\form;

use zibo\install\view\InstallStepRequirementView;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\validation\exception\ValidationException;
use zibo\library\wizard\step\AbstractWizardStep;

/**
 * Step 2 of the Zibo installation: requirements
 */
class InstallStepRequirement extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepRequirement';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        $installer = $this->wizard->getInstaller();

        $profile = $this->wizard->getProfile();
        $requirements = $installer->getRequirements($profile);

        $translator = I18n::getInstance()->getTranslator();

        return new InstallStepRequirementView($requirements, $translator);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {

    }

    /**
     * Checks if this step has a next step
     * @return boolean
     */
    public function hasNext() {
        $installer = $this->wizard->getInstaller();
        $profile = $this->wizard->getProfile();

        return $installer->hasRequirementsMet($profile);
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

        return InstallStepLocalization::NAME;
    }

    /**
     * Processes the previous action of this step
     * return string Name of the previous step
     */
    public function previous() {
        return InstallStepProfile::NAME;
    }

}