<?php

namespace zibo\orm\log\view;

use zibo\admin\view\BaseView;

use zibo\orm\scaffold\table\LogChangeTable;
use zibo\orm\scaffold\view\LogView as ScaffoldLogView;

/**
 * View for a log of a model
 */
class LogView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/log/log';

    /**
     * Constructs a new log view
     * @return null
     */
    public function __construct($log, $urlBack) {
        parent::__construct(self::TEMPLATE);

        $table = new LogChangeTable($log->changes);

        $this->set('log', $log);
        $this->set('table', $table);
        $this->set('urlBack', $urlBack);

        $this->addStyle(ScaffoldLogView::STYLE);
    }

}