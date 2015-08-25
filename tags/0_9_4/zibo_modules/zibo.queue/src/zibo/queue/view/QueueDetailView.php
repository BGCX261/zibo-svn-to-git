<?php

namespace zibo\queue\view;

use zibo\admin\view\BaseView;

use zibo\queue\model\data\QueueData;

/**
 * View for the detail of a job
 */
class QueueDetailView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'queue/detail';

    /**
     * Path to the stylesheet of this view
     * @var string
     */
    const STYLE = 'web/styles/queue.css';

    /**
     * Constructs the queue detail view
     * @param zibo\queue\model\data\QueueData $data
     * @param string $statusUrl
     * @return null
     */
    public function __construct(QueueData $data, $backUrl, $statusUrl) {
        parent::__construct(self::TEMPLATE);

        $this->set('data', $data);
        $this->set('backUrl', $backUrl);

        $this->addJavascript(self::SCRIPT_TABLE);
        $this->addStyle(self::STYLE);
    }

}