<?php

namespace zibo\install\model\profile;

use zibo\admin\model\module\Module;

use zibo\install\form\InstallStepDatabase;
use zibo\install\model\requirement\DatabaseRequirement;

use zibo\library\i18n\translation\Translator;

/**
 * Abstract implementation of a installation profile
 */
class DatabaseProfile extends AbstractProfile {

    /**
     * Translation key for the name of this profile
     * @var string
     */
    const TRANSLATION_NAME = 'install.profile.database';

    /**
     * Translation key for the description of this profile
     * @var string
     */
    const TRANSLATION_DESCRIPTION = 'install.profile.database.description';

    /**
     * Constructs a new profile
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::TRANSLATION_DESCRIPTION);
    }

    /**
     * Gets the extra requirements for this profile
     * @return array
     */
    public function getRequirements() {
        return array(
            new DatabaseRequirement(),
        );
    }

    /**
     * Gets the extra modules needed for this profile
     * @return array Array with basic Module instances
     * @see zibo\admin\model\module\Module
     */
    public function getModules() {
        return array(
            new Module('zibo', 'database'),
            new Module('zibo', 'database.admin'),
            new Module('zibo', 'database.mysql'),
            new Module('zibo', 'orm'),
        );
    }

    /**
     * Gets extra wizard steps needed for this profile, these steps should come between the localization and the installation step
     * @return array Array with the name of the wizard step as key and an instance of WizardStep as value
     */
    public function getWizardSteps() {
        return array(
            InstallStepDatabase::NAME => new InstallStepDatabase(),
        );
    }

}