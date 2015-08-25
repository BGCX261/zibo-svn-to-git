<?php

namespace zibo\orm\builder\view\wizard;

use zibo\library\i18n\I18n;
use zibo\library\orm\definition\ModelTable;
use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\wizard\BuilderWizard;
use zibo\orm\builder\wizard\FieldStep;
use zibo\orm\builder\wizard\FinishStep;
use zibo\orm\builder\wizard\IndexStep;
use zibo\orm\builder\wizard\DataFormatStep;
use zibo\orm\builder\wizard\ModelGeneralStep;

/**
 * View for the model filter
 */
class SidebarView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/sidebar';

    const TRANSLATION_STEP_GENERAL = 'orm.label.wizard.general';
    const TRANSLATION_STEP_GENERAL_MODEL = 'orm.label.wizard.general.model';
    const TRANSLATION_STEP_FIELD = 'orm.label.wizard.field';
    const TRANSLATION_STEP_FIELD_NEW = 'orm.label.wizard.field.new';
    const TRANSLATION_STEP_DATA_FORMAT = 'orm.label.wizard.format';
    const TRANSLATION_STEP_INDEX = 'orm.label.wizard.index';
    const TRANSLATION_STEP_FINISH = 'orm.label.wizard.finish';

    /**
     * Construct a new model filter view
     * @param zibo\orm\builder\form\ModelFilterForm $filterForm
     * @return null
     */
    public function __construct(BuilderWizard $wizard) {
        parent::__construct(self::TEMPLATE);

        $translator = I18n::getInstance()->getTranslator();
        $modelTable = $wizard->getModelTable();

        $currentStep = $wizard->getCurrentStep();
        $steps = array();

        if ($modelTable) {
            $generalStep = $translator->translate(self::TRANSLATION_STEP_GENERAL_MODEL, array('model' => $modelTable->getName()));
        } else {
            $generalStep = $translator->translate(self::TRANSLATION_STEP_GENERAL);
        }
        $fieldNewStep = $translator->translate(self::TRANSLATION_STEP_FIELD_NEW);
        $dataFormatStep = $translator->translate(self::TRANSLATION_STEP_DATA_FORMAT);
        $indexStep = $translator->translate(self::TRANSLATION_STEP_INDEX);
        $finishStep = $translator->translate(self::TRANSLATION_STEP_FINISH);

        switch ($currentStep) {
            case ModelGeneralStep::NAME:
                $currentStep = $generalStep;
                break;
            case DataFormatStep::NAME:
                $currentStep = $dataFormatStep;
                break;
            case IndexStep::NAME:
                $currentStep = $indexStep;
                break;
            case FinishStep::NAME:
                $currentStep = $finishStep;
                break;
        }

        if ($wizard->isLimitedToModel()) {
            $steps[$generalStep] = false;
        } elseif ($wizard->isLimitedToDataFormats()) {
            $steps[$dataFormatStep] = false;
        } elseif ($wizard->isLimitedToIndex()) {
            $steps[$indexStep] = false;
        } else {
            $limitField = $wizard->getLimitField();

            if (!$limitField) {
                $steps[$generalStep] = false;
            }

            if ($limitField && $limitField !== true) {
                $step = $translator->translate(self::TRANSLATION_STEP_FIELD, array('field' => $limitField));

                if ($currentStep == FieldStep::NAME) {
                    $currentStep = $step;
                }

                $steps[$step] = false;
            } else {
                $isFieldStep = $currentStep == FieldStep::NAME;
                $currentFieldName = null;
                if ($isFieldStep) {
                    $currentFieldName = $wizard->getVariable(BuilderWizard::VARIABLE_FIELD);
                    $currentStep = null;
                }

                if ($modelTable) {
                    $fields = $modelTable->getFields();
                    foreach ($fields as $fieldName => $field) {
                        if ($fieldName == ModelTable::PRIMARY_KEY) {
                            continue;
                        }

                        $step = $translator->translate(self::TRANSLATION_STEP_FIELD, array('field' => $fieldName));

                        if ($fieldName == $currentFieldName) {
                            $currentStep = $step;
                        }

                        $steps[$step] = false;
                    }
                }

                if (!$currentStep) {
                    $currentStep = $fieldNewStep;
                }

                $steps[$fieldNewStep] = false;
            }

            if (!$limitField) {
                $steps[$dataFormatStep] = false;
                $steps[$indexStep] = false;
            }
        }

        $steps[$finishStep] = false;

        $this->set('currentStep', $currentStep);
        $this->set('steps', $steps);
    }

}