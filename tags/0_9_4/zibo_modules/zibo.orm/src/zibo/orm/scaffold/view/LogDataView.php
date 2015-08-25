<?php

namespace zibo\orm\scaffold\view;

use zibo\admin\controller\LocalizeController;

use zibo\library\orm\model\LogModel;
use zibo\library\orm\ModelManager;
use zibo\library\smarty\view\SmartyView;

use zibo\orm\scaffold\table\LogChangeTable;

/**
 * View for the data of a orm data history
 */
class LogDataView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/scaffold/log.data';

    /**
     * Constructs a new log data view
     * @param string $modelName Name of the model
     * @param int $id Primary key of the data
     * @return null
     */
    public function __construct(array $modelLogs) {
        parent::__construct(self::TEMPLATE);

        $logs = array();
        foreach ($modelLogs as $id => $modelLog) {
            $table = new LogChangeTable($modelLog->changes);

            $log = array(
                'log' => $modelLog,
                'table' => $table,
            );

            $logs[] = $log;
        }

        $this->set('logs', $logs);
    }

}