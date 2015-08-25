<?php

namespace zibo\spider\view;

use zibo\library\spider\WebNode;
use zibo\library\smarty\view\SmartyView;

class ReportDetailView extends SmartyView {

    const TEMPLATE = 'spider/report.detail';

    public function __construct(WebNode $node) {
        parent::__construct(self::TEMPLATE);

        $this->set('node', $node);
    }

}