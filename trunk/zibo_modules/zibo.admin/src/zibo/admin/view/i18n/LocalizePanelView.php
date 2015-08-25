<?php

namespace zibo\admin\view\i18n;

use zibo\admin\form\LocalizePanelForm;

use zibo\library\smarty\view\SmartyView;

/**
 * Panel to see the and change the locale of the content which is edited
 */
class LocalizePanelView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/i18n/localize.panel';

    /**
     * Path to the JS script of this view
     * @var string
     */
    const SCRIPT_LOCALIZE = 'web/scripts/admin/localize.js';

    /**
     * Constructs a new localize panel view
     * @param boolean $enabled True to enable the localize form, false to disable it
     * @return null
     */
    public function __construct($enabled = true) {
        parent::__construct(self::TEMPLATE);

        $form = new LocalizePanelForm();
        if ($enabled) {
            $this->addJavascript(self::SCRIPT_LOCALIZE);
            $this->addInlineJavascript('ziboAdminInitializeLocalizePanel();');
        } else {
            $form->setIsDisabled(true);
        }

        $this->set('form', $form);
    }

}