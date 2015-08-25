<?php

namespace zibo\orm\builder\view\wizard;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\wizard\BuilderWizard;

/**
 * View for the field step of the model wizard
 */
class FieldStepView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/step.field';

    /**
     * Constructs a new step view
     * @param zibo\orm\builder\wizard\BuilderWizard $wizard
     */
    public function __construct(BuilderWizard $wizard, $fieldsAction) {
        parent::__construct(self::TEMPLATE);


        $modelTable = $wizard->getVariable(BuilderWizard::VARIABLE_MODEL_TABLE);
        $fields = $modelTable->getFields();
        $isFieldNameRequired = count($fields) <= 1;

        $this->set('wizard', $wizard);
        $this->set('isFieldNameRequired', $isFieldNameRequired);

        $this->addInlineJavascript('ziboOrmInitializeBuilderWizardField(' . ($isFieldNameRequired ? 'true' : 'false') . ', "' . $fieldsAction . '");');
    }

}