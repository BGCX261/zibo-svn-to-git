<?php

namespace zibo\error\report\view;

use zibo\admin\view\BaseView;

use zibo\error\report\form\ReportForm;

/**
 * View to report an error to the developers
 */
class ReportView extends BaseView {

    /**
     * Construct a new error report view
     * @param zibo\error\report\form\ReportForm $form
     * @param string $report
     * @return null
     */
    public function __construct(ReportForm $form, $report) {
        parent::__construct('error/report');

        $this->set('form', $form);
        $this->set('report', $report);

        $this->addStyle('web/styles/error/report.css');
    }

}