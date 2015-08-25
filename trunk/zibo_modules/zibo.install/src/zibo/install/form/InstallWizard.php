<?php

namespace zibo\install\form;

use zibo\install\model\Installer;

use zibo\library\wizard\Wizard;

use zibo\ZiboException;

/**
 * Wizard of the Zibo installation
 */
class InstallWizard extends Wizard {

    /**
     * Name of the wizard
     * @var string
     */
    const NAME = 'wizardInstall';

    /**
     * Name of the profile variable
     * @var string
     */
    const VAR_PROFILE = 'profile';

    /**
     * Name of the languages variable
     * @var string
     */
    const VAR_LANGUAGES = 'languages';

    /**
     * The installer logic
     * @var zibo\install\model\Installer
     */
    private $installer;

    /**
     * Constructs a new install wizard
     * @param string $action URL where the wizard form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME);

        $this->installer = new Installer();

        $this->addStep(InstallStepProfile::NAME, new InstallStepProfile());
        $this->addStep(InstallStepRequirement::NAME, new InstallStepRequirement());
        $this->addStep(InstallStepLocalization::NAME, new InstallStepLocalization());
        $this->addStep(InstallStepInstallation::NAME, new InstallStepInstallation());
        $this->addStep(InstallStepFinish::NAME, new InstallStepFinish());

        $steps = $this->getProfileSteps();
        foreach ($steps as $name => $step) {
            $this->addStep($name, $step);
        }
    }

    /**
     * Sets the name of the installation profile to use
     * @param string $profile The name of the profile
     * @return null
     */
    public function setProfile($profile) {
        $this->setVariable(self::VAR_PROFILE, $profile);
    }

    /**
     * Gets the name of the installation profile
     * @return string
     */
    public function getProfile() {
        return $this->getVariable(self::VAR_PROFILE);
    }

    /**
     * Sets the languages which should be installed
     * @param array $languages Array with the language codes
     * @return null
     */
    public function setLanguages($languages) {
        $this->setVariable(self::VAR_LANGUAGES, $languages);
    }

    /**
     * Gets the languages which should be installed
     * @return string
     */
    public function getLanguages() {
        return $this->getVariable(self::VAR_LANGUAGES, array('en'));
    }

    /**
     * Gets the Zibo installer
     * @return zibo\install\model\Installer
     */
    public function getInstaller() {
        return $this->installer;
    }

    /**
     * Gets the next dynamic step based on the profile
     * @return string The name of the next step
     */
    public function getNextStep() {
        $found = false;
        $firstStep = null;
        $nextStep = null;
        $currentStep = $this->getCurrentStep();

        $steps = $this->getProfileSteps();
        foreach ($steps as $name => $step) {
            if ($firstStep == null) {
                $firstStep = $name;
            }

            if ($found) {
                $nextStep = $name;
                break;
            }

            if ($name == $currentStep) {
                $found = true;
            }
        }

        if ((!$nextStep && $found) || !$steps) {
            $nextStep = InstallStepFinish::NAME;
        } elseif (!$nextStep) {
            $nextStep = $firstStep;
        }

        return $nextStep;
    }

    /**
     * Gets the previous dynamic step based on the profile
     * @return string The name of the next step
     */
    public function getPreviousStep() {
        $found = false;
        $previousStep = null;
        $currentStep = $this->getCurrentStep();

        $steps = $this->getProfileSteps();
        foreach ($steps as $name => $step) {
            if ($name == $currentStep) {
                $found = true;
                break;
            }

            $previousStep = $name;
        }

        if (!$found || !$previousStep) {
            $previousStep = InstallStepInstallation::NAME;
        }

        return $previousStep;
    }

    /**
     * Gets the extra wizard steps for the selected profile
     * @return array
     */
    private function getProfileSteps() {
        $profile = $this->getProfile();
        if (!$profile) {
            return array();
        }

        $profile = $this->installer->getProfile($profile);

        return $profile->getWizardSteps();
    }

}