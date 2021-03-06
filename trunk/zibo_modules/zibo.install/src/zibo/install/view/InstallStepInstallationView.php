<?php

namespace zibo\install\view;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the actual installation step of the Zibo installation
 */
class InstallStepInstallationView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'install/step.installation';

    /**
     * Constructs a new view for the Zibo installation
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TEMPLATE);
    }

}