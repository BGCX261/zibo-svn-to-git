<?php

namespace zibo\orm\log\view;

use zibo\library\smarty\view\SmartyView;

use zibo\orm\log\form\LogFilterForm;

/**
 * View for the log filter
 */
class LogFilterView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/log/log.filter';

    /**
     * Construct a new log filter view
     * @param zibo\orm\log\form\LogFilterForm $filterForm
     * @return null
     */
    public function __construct(LogFilterForm $filterForm) {
        parent::__construct(self::TEMPLATE);

        $this->set('filterForm', $filterForm);
    }

}