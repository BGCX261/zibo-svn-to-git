<?php

namespace zibo\orm\builder\view\wizard;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\wizard\BuilderWizard;

/**
 * View for the general info step of the model wizard
 */
class ModelGeneralStepView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/step.general';

    /**
     * Constructs a new step view
     * @param zibo\orm\builder\wizard\BuilderWizard $wizard
     */
    public function __construct(BuilderWizard $wizard) {
        parent::__construct(self::TEMPLATE);

        $this->set('wizard', $wizard);

        $this->addInlineJavascript('ziboOrmInitializeBuilderWizardGeneral();');
    }

}