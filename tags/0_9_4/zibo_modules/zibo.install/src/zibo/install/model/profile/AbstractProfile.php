<?php

namespace zibo\install\model\profile;

use zibo\library\i18n\translation\Translator;

/**
 * Abstract implementation of a installation profile
 */
abstract class AbstractProfile implements Profile {

    /**
     * Translation key for the name of this profile
     * @var string
     */
    protected $name;

    /**
     * Translation key for the description of this profile
     * @var string
     */
    protected $description;

    /**
     * Constructs a new profile
     * @param string $name The translation key for the name of this profile
     * @param string $description The translation key for the description of this profile
     * @return null
     */
    public function __construct($name, $description = null) {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Gets the name of this profile
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getName(Translator $translator) {
        return $translator->translate($this->name);
    }

    /**
     * Gets the description of this profile
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getDescription(Translator $translator) {
        if ($this->description) {
            return $translator->translate($this->description);
        }

        return '';
    }

    /**
     * Gets the extra requirements for this profile
     * @return array
     */
    public function getRequirements() {
        return array();
    }

    /**
     * Gets the extra modules needed for this profile
     * @return array Array with basic Module instances
     * @see zibo\admin\model\Module
     */
    public function getModules() {
        return array();
    }

    /**
     * Gets extra wizard steps needed for this profile, these steps should come between the localization and the installation step
     * @return array Array with the name of the wizard step as key and an instance of WizardStep as value
     */
    public function getWizardSteps() {
        return array();
    }

    /**
     * Performs extra installation logic after the modules have been installed
     * @return null
     */
    public function install() {

    }


}