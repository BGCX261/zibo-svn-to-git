<?php

namespace zibo\install\view;

use zibo\install\form\InstallWizard;

use zibo\library\smarty\view\SmartyView;
use zibo\library\wizard\Wizard;

/**
 * View for the profile select step of the Zibo installation
 */
class InstallStepProfileView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'install/step.profile';

    /**
     * Constructs a new view for the Zibo installation
     * @return null
     */
    public function __construct(InstallWizard $wizard) {
        parent::__construct(self::TEMPLATE);

        $this->set('wizard', $wizard);
    }

}