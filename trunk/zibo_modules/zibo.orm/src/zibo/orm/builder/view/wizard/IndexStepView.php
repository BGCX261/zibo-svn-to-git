<?php

namespace zibo\orm\builder\view\wizard;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\table\SimpleModelIndexTable;
use zibo\orm\builder\wizard\BuilderWizard;

/**
 * View for the index step of the model wizard
 */
class IndexStepView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/step.index';

    /**
     * Constructs a new step view
     * @param zibo\orm\builder\wizard\BuilderWizard $wizard
     */
    public function __construct(BuilderWizard $wizard, SimpleModelIndexTable $indexTable) {
        parent::__construct(self::TEMPLATE);

        $this->set('wizard', $wizard);
        $this->set('indexTable', $indexTable);

        $this->addInlineJavascript('ziboOrmInitializeBuilderWizardIndex();');
    }

}