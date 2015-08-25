<?php

namespace zibo\orm\builder\view\wizard;

use zibo\admin\view\BaseView;

use zibo\library\orm\definition\ModelTable;
use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\table\SimpleDataFormatTable;
use zibo\orm\builder\table\SimpleModelFieldTable;
use zibo\orm\builder\table\SimpleModelIndexTable;
use zibo\orm\builder\wizard\BuilderWizard;

/**
 * View for the finish step of the model wizard
 */
class FinishStepView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/step.finish';

    /**
     * Constructs a new step view
     * @param zibo\orm\builder\wizard\BuilderWizard $wizard
     */
    public function __construct(BuilderWizard $wizard, ModelTable $modelTable, SimpleModelFieldTable $fieldTable, SimpleDataFormatTable $formatTable, SimpleModelIndexTable $indexTable) {
        parent::__construct(self::TEMPLATE);

        $this->set('wizard', $wizard);
        $this->set('modelTable', $modelTable);
        $this->set('modelClass', $wizard->getVariable(BuilderWizard::VARIABLE_MODEL_CLASS));
        $this->set('dataClass', $wizard->getVariable(BuilderWizard::VARIABLE_DATA_CLASS));
        $this->set('fieldTable', $fieldTable);
        $this->set('formatTable', $formatTable);
        $this->set('indexTable', $indexTable);

        $this->addJavascript(BaseView::SCRIPT_TABLE);
    }

}