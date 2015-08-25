<?php

namespace zibo\orm\builder\view;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\builder\form\ModelFilterForm;

/**
 * View for the model filter
 */
class ModelFilterView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/builder/model.filter';

    /**
     * Construct a new model filter view
     * @param zibo\orm\builder\form\ModelFilterForm $filterForm
     * @return null
     */
    public function __construct(ModelFilterForm $filterForm) {
        parent::__construct(self::TEMPLATE);

        $this->set('filterForm', $filterForm);
    }

}