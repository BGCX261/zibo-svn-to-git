<?php

namespace zibo\admin\view\modules;

use zibo\admin\form\ModuleInstallForm;
use zibo\admin\table\ModulesTable;
use zibo\admin\view\BaseView;

/**
 * View of the installed modules
 */
class ModulesView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/modules/overview';

    /**
     * Path to the JS script of this view
     * @var string
     */
    const SCRIPT_MODULES = 'web/scripts/admin/modules.js';

    /**
     * Path to the CSS script of this view
     * @var string
     */
    const STYLE_MODULES = 'web/styles/admin/modules.css';

    /**
     * Constructs a new modules view
     * @param zibo\admin\form\ModuleInstallForm $form
     * @param zibo\admin\table\ModulesTable $table
     * @return null
     */
    public function __construct(ModuleInstallForm $form, ModulesTable $table) {
        parent::__construct(self::TEMPLATE);

        $this->set('table', $table);

        $this->sidebar->addPanel(new FormView($form));

        $this->addStyle(self::STYLE_MODULES);
        $this->addJavascript(self::SCRIPT_MODULES);
        $this->addJavascript(self::SCRIPT_TABLE);
        $this->addInlineJavascript('ziboAdminInitializeModules();');
    }

}