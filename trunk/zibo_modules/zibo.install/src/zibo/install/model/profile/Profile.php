<?php

namespace zibo\install\model\profile;

use zibo\library\i18n\translation\Translator;

/**
 * Interface for a installation profile
 */
interface Profile {

    /**
     * Gets the name of the profile
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getName(Translator $translator);

    /**
     * Gets the description of the profile
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getDescription(Translator $translator);

    /**
     * Gets the extra requirements for this profile
     * @return array Array with Requirement instances
     * @see zibo\install\model\requirement\Requirement
     */
    public function getRequirements();

    /**
     * Gets the extra modules needed for this profile
     * @return array Array with basic Module instances
     * @see zibo\admin\model\module\Module
     */
    public function getModules();

    /**
     * Gets extra wizard steps needed for this profile, these steps should come between the localization and the installation step
     * @return array Array with the name of the wizard step as key and an instance of WizardStep as value
     */
    public function getWizardSteps();

    /**
     * Performs extra installation logic after the modules have been installed
     * @return null
     */
    public function install();

}