<?php

namespace zibo\orm\builder\view\wizard;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\table\SimpleDataFormatTable;
use zibo\orm\builder\wizard\BuilderWizard;

/**
 * View for the extra info of the model wizard
 */
class DataFormatStepView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/step.format';

    /**
     * Constructs a new step view
     * @param zibo\orm\builder\wizard\BuilderWizard $wizard
     * @param zibo\orm\builder\table\SimpleDateFormatTable $formatTable
     * @return null
     */
    public function __construct(BuilderWizard $wizard, SimpleDataFormatTable $formatTable) {
        parent::__construct(self::TEMPLATE);

        $this->set('wizard', $wizard);
        $this->set('formatTable', $formatTable);

        $this->addInlineJavascript('ziboOrmInitializeBuilderWizardFormat();');
    }

}