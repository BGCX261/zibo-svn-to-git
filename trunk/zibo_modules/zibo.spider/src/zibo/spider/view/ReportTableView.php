<?php

namespace zibo\spider\view;

use zibo\library\smarty\view\SmartyView;

class ReportTableView extends SmartyView {

    const TEMPLATE = 'spider/report.table';

    public function __construct(array $nodes) {
        parent::__construct(self::TEMPLATE);

        $this->set('nodes', $nodes);
    }

}