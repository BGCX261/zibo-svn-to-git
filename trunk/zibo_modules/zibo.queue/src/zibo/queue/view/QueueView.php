<?php

namespace zibo\queue\view;

use zibo\admin\view\BaseView;

use zibo\queue\table\QueueTable;

/**
 * View for the queue overview
 */
class QueueView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'queue/index';

    /**
     * Path to the stylesheet of this view
     * @var string
     */
    const STYLE = 'web/styles/queue.css';

    /**
     * Constructs the queue overview
     * @param zibo\queue\table\QueueTable $table The table with the overview
     * @return null
     */
    public function __construct(QueueTable $table) {
        parent::__construct(self::TEMPLATE);

        $this->set('table', $table);

        $this->addJavascript(self::SCRIPT_TABLE);
        $this->addStyle(self::STYLE);
    }

}