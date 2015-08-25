<?php

namespace zibo\repository\view;

use zibo\repository\model\Module;
use zibo\repository\table\ModuleVersionTable;

/**
 * Detail view of a module in the repository
 */
class ModuleView extends AbstractRepositoryView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'repository/module';

    /**
     * Constructs a new module detail view
     * @param zibo\repository\model\Module $module Module to display in detail
     * @param zibo\repository\table\ModuleVersionTable $table Table with an overview of the module versions
     * @param string $urlBack URL to go back to the previous page
     * @param string $urlModule Base URL for a module detail link, namespace name and module name will be concatted to this link
     * @param string $translationAction Translation key for the main detail action
     * @param string $urlAction URL for the mail detail action
     * @return null
     */
    public function __construct(Module $module, ModuleVersionTable $table, $urlBack = null, $urlModule = null, $translationAction = null, $urlAction = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('module', $module);
        $this->set('table', $table);
        $this->set('urlBack', $urlBack);
        $this->set('urlModule', $urlModule);
        $this->set('translationAction', $translationAction);
        $this->set('urlAction', $urlAction);
    }

}