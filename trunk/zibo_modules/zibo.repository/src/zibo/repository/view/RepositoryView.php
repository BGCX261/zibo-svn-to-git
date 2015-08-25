<?php

namespace zibo\repository\view;

use zibo\library\html\table\Table;
use zibo\library\html\Breadcrumbs;

use zibo\repository\form\ModuleUploadForm;

/**
 * View for the repository
 */
class RepositoryView extends AbstractRepositoryView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'repository/repository';

    /**
     * Constructs a new repository view
     * @param string $title Title for the view
     * @param zibo\library\html\table\Table $table Table with the namespaces or modules
     * @param zibo\repository\form\ModuleUploadForm $form Form to upload a new module
     * @param zibo\library\html\Breadcrumbs $breadcrumbs Breadcrumbs of the repository navigation
     * @return null
     */
    public function __construct($title, Table $table, ModuleUploadForm $form = null, Breadcrumbs $breadcrumbs = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('title', $title);
        $this->set('table', $table);
        $this->set('form', $form);
        $this->set('breadcrumbs', $breadcrumbs);
    }

}