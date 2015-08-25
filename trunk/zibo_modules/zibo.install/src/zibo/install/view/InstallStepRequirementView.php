<?php

namespace zibo\install\view;

use zibo\library\i18n\translation\Translator;
use zibo\library\smarty\view\SmartyView;
use zibo\library\wizard\Wizard;

/**
 * View for the requirements step of the Zibo installation
 */
class InstallStepRequirementView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'install/step.requirement';

    /**
     * Constructs a new view for the Zibo installation
     * @return null
     */
    public function __construct(array $requirements, Translator $translator) {
        parent::__construct(self::TEMPLATE);

        $this->set('requirements', $requirements);
        $this->set('translator', $translator);
    }

}