<?php

namespace zibo\spider\view;

use zibo\library\i18n\I18n;
use zibo\library\smarty\view\SmartyView;

class ReportView extends SmartyView {

    const TEMPLATE = 'spider/report';

    public function __construct(array $reports) {
        parent::__construct(self::TEMPLATE);

        $this->set('translator', I18n::getInstance()->getTranslator());
        $this->set('reports', $reports);

        foreach ($reports as $index => $report) {
            $this->setSubview('report' . $index, $report->getView());
        }
    }

}