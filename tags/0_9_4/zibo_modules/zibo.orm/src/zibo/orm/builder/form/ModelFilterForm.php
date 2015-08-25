<?php

namespace zibo\orm\builder\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\I18n;

/**
 * Form to filter the displayed models
 */
class ModelFilterForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formModelFilter';

    /**
     * Value to include custom models
     * @var string
     */
    const INCLUDE_CUSTOM = 'custom';

    /**
     * Value to include module models
     * @var string
     */
    const INCLUDE_MODULE = 'module';

    /**
     * Value to include localized models
     * @var string
     */
    const INCLUDE_LOCALIZED = 'localized';

    /**
     * Value to include link models
     * @var string
     */
    const INCLUDE_LINK = 'link';

    /**
     * Name of the include module models field
     * @var string
     */
    const FIELD_INCLUDE = 'include';

    /**
     * Translation key for the include custom models
     * @var string
     */
    const TRANSLATION_CUSTOM = 'orm.label.filter.model.custom';

    /**
     * Translation key for the include module models
     * @var string
     */
    const TRANSLATION_MODULE = 'orm.label.filter.model.module';

    /**
     * Translation key for the include localized models
     * @var string
     */
    const TRANSLATION_LOCALIZED = 'orm.label.filter.model.localized';

    /**
     * Translation key for the include link models
     * @var string
     */
    const TRANSLATION_LINK = 'orm.label.filter.model.link';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_FILTER = 'button.filter';

    /**
     * Constructs a new model filter form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action, array $include = null) {
        parent::__construct($action, self::NAME, self::TRANSLATION_FILTER);

        $translator = I18n::getInstance()->getTranslator();

        $includeOptions = array(
            self::INCLUDE_CUSTOM => $translator->translate(self::TRANSLATION_CUSTOM),
            self::INCLUDE_MODULE => $translator->translate(self::TRANSLATION_MODULE),
            self::INCLUDE_LOCALIZED => $translator->translate(self::TRANSLATION_LOCALIZED),
            self::INCLUDE_LINK => $translator->translate(self::TRANSLATION_LINK),
        );

        if ($include == null) {
            $include = array(
                self::INCLUDE_CUSTOM => self::INCLUDE_CUSTOM,
                self::INCLUDE_MODULE => self::INCLUDE_MODULE
            );
        }

        $fieldFactory = FieldFactory::getInstance();

        $fieldInclude = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_INCLUDE, $include);
        $fieldInclude->setIsMultiple(true);
        $fieldInclude->setOptions($includeOptions);

        $this->addField($fieldInclude);
    }

    /**
     * Gets whether to include custom models
     * @return boolean
     */
    public function includeCustomModels() {
        return $this->includeModels(self::INCLUDE_CUSTOM);
    }

    /**
     * Gets whether to include module models
     * @return boolean
     */
    public function includeModuleModels() {
        return $this->includeModels(self::INCLUDE_MODULE);
    }

    /**
     * Gets whether to include localized models
     * @return boolean
     */
    public function includeLocalizedModels() {
        return $this->includeModels(self::INCLUDE_LOCALIZED);
    }

    /**
     * Gets whether to include link models
     * @return boolean
     */
    public function includeLinkModels() {
        return $this->includeModels(self::INCLUDE_LINK);
    }

    /**
     * Gets whether to include models
     * @param string $type
     * @return boolean
     */
    private function includeModels($type) {
        $include = $this->getValue(self::FIELD_INCLUDE);
        if ($include === null) {
            $include = $this->getField(self::FIELD_INCLUDE)->getDefaultValue();
        }

        if (array_key_exists($type, $include)) {
            return true;
        }

        return false;
    }

}