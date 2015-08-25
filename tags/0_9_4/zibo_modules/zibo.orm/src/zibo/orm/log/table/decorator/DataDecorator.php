<?php

namespace zibo\orm\log\table\decorator;

use zibo\core\Zibo;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\orm\model\LogModel;
use zibo\library\orm\ModelManager;

use \Exception;

/**
 * Decorator for a log data object
 */
class DataDecorator implements Decorator {

    /**
     * URL where the title of the data will point to
     * @var string
     */
    private $action;

    /**
     * Instance of the model manager
     * @var zibo\library\orm\ModelManager
     */
    private $modelManager;

    /**
     * Instance of the log model
     * @var zibo\library\orm\model\LogModel
     */
    private $logModel;

    /**
     * Constructs a new log decorator
     * @param string $action URL where the log will point to
     * @return null
     */
    public function __construct($action = null) {
        $this->action = $action;
        $this->modelManager = ModelManager::getInstance();
        $this->logModel = $this->modelManager->getModel(LogModel::NAME);
    }

    /**
     * Decorates the data in the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $log = $cell->getValue();

        $value = $log->dataModel . ' #' . $log->dataId;
        if ($this->action) {
            $anchor = new Anchor($value, $this->action . $log->id);
            $value = $anchor->getHtml();
        }

        $data = null;

        try {
            $model = $this->modelManager->getModel($log->dataModel);
            $data = $this->logModel->getDataByVersion($log->dataModel, $log->dataId, $log->dataVersion);
            $data = $model->getMeta()->formatData($data);
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
        }

        if (!$data) {
            $data = '---';
        }

        $value .= '<div class="info">' . $data . '</div>';

        $cell->setValue($value);
    }

}