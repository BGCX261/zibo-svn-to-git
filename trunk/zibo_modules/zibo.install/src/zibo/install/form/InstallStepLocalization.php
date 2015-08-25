<?php

namespace zibo\install\form;

use zibo\install\view\InstallStepLocalizationView;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\validation\exception\ValidationException;
use zibo\library\wizard\step\AbstractWizardStep;

/**
 * Step 3 of the Zibo installation: localization
 */
class InstallStepLocalization extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepLocalization';

    /**
     * Name of the languages field
     * @var string
     */
    const FIELD_LANGUAGES = 'languages';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new InstallStepLocalizationView($this->wizard);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $fieldFactory = FieldFactory::getInstance();
        $translator = I18n::getInstance()->getTranslator();

        $installer = $this->wizard->getInstaller();
        $languages = $installer->getLanguages();

        $selectedLanguages = $this->wizard->getLanguages();
        $selectedLanguages = array_flip($selectedLanguages);

        $languageField = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_LANGUAGES, $selectedLanguages);
        $languageField->setOptions($languages);
        $languageField->setIsMultiple(true);

        $this->wizard->addField($languageField);
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

        $languages = $this->wizard->getValue(self::FIELD_LANGUAGES);
        $languages['en'] = 'English';

        $this->wizard->setLanguages(array_keys($languages));

        return InstallStepInstallation::NAME;
    }

    /**
     * Processes the previous action of this step
     * return string Name of the previous step
     */
    public function previous() {
        return InstallStepRequirement::NAME;
    }

}