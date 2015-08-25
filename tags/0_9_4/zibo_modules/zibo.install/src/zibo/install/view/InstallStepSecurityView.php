<?php

namespace zibo\install\view;

use zibo\install\form\InstallWizard;

use zibo\library\smarty\view\SmartyView;
use zibo\library\wizard\Wizard;

/**
 * View for the database step of the Zibo installation
 */
class InstallStepSecurityView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'install/step.security';

    /**
     * Constructs a new view for the Zibo installation
     * @return null
     */
    public function __construct(InstallWizard $wizard) {
        parent::__construct(self::TEMPLATE);

        $this->set('wizard', $wizard);
    }

}