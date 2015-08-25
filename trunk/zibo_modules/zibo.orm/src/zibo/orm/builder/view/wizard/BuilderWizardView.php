<?php

namespace zibo\orm\builder\view\wizard;

use zibo\admin\view\BaseView;

use zibo\core\View;

/**
 * View for the model wizard
 */
class BuilderWizardView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/wizard/index';

    /**
     * Path to the JS file of this view
     * @var string
     */
    const SCRIPT_WIZARD = 'web/scripts/orm/wizard.js';

    /**
     * Path to the CSS file of this view
     * @var string
     */
    const STYLE_WIZARD = 'web/styles/orm/wizard.css';

    /**
     * Constructs a new view for the model wizard
     * @param zibo\core\View $wizardView
     * @return null
     */
    public function __construct(View $wizardView = null) {
        parent::__construct(self::TEMPLATE);

        if ($wizardView) {
            $this->setSubview('wizard', $wizardView);
        }

        $this->addStyle(self::STYLE_WIZARD);
        $this->addJavascript(self::SCRIPT_WIZARD);
    }

}