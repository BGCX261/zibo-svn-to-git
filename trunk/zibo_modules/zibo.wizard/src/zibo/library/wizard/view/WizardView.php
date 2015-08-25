<?php

namespace zibo\library\wizard\view;

use zibo\core\View;

use zibo\library\smarty\view\SmartyView;
use zibo\library\wizard\Wizard;

/**
 * View for a wizard
 */
class WizardView extends SmartyView {

    /**
-    * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'wizard/wizard';

    /**
     * Constructs a new wizard view
     * @param zibo\library\wizard\Wizard $wizard The wizard
     * @param zibo\core\View $stepView View of the current step
     * @param
     * @return null
     */
    public function __construct(Wizard $wizard, View $stepView = null, $template = null) {
        if (!$template) {
            $template = self::TEMPLATE;
        }

        parent::__construct($template);

        $this->set('wizard', $wizard);

        if ($stepView) {
            $this->setSubview('wizardStep', $stepView);
        }
    }

}