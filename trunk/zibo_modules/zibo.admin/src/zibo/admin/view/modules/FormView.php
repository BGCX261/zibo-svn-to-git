<?php

namespace zibo\admin\view\modules;

use zibo\admin\form\ModuleInstallForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the install form
 */
class FormView extends SmartyView {

    /**
     * Path to the template of the form
     * @var string
     */
    const TEMPLATE = 'admin/modules/form';

    /**
     * Constructs a new form view
     * @param zibo\admin\form\ModuleInstallForm $form
     * @return null
     */
    public function __construct(ModuleInstallForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);
    }

}