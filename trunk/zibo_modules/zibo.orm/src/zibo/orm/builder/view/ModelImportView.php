<?php

namespace zibo\orm\builder\view;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\form\ModelImportForm;

/**
 * View for the model import form
 */
class ModelImportView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/model.import';

    /**
     * Construct a new model import view
     * @param zibo\orm\builder\form\ModelImportForm $importForm
     * @return null
     */
    public function __construct(ModelImportForm $importForm) {
        parent::__construct(self::TEMPLATE);

        $this->set('importForm', $importForm);
    }

}