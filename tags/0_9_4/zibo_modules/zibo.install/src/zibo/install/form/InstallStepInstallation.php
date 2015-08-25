<?php

namespace zibo\install\form;

use zibo\install\view\InstallStepInstallationView;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\validation\exception\ValidationException;
use zibo\library\wizard\step\AbstractWizardStep;

/**
 * Step 3 of the Zibo installation: installation
 */
class InstallStepInstallation extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepInstallation';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new InstallStepInstallationView();
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {

    }

    /**
     * Processes the next action of this step
     * return string Name of the next step
     */
    public function next() {
        $profile = $this->wizard->getProfile();
        $languages = $this->wizard->getLanguages();

        $installer = $this->wizard->getInstaller();
        $installer->installProfile($profile, $languages);

        return $this->wizard->getNextStep();
    }

    /**
     * Processes the previous action of this step
     * return string Name of the previous step
     */
    public function previous() {
        return $this->wizard->getPreviousStep();
    }

}