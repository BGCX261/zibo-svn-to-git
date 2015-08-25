<?php

namespace zibo\orm\scaffold\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\controller\LocalizeController;

use zibo\library\orm\model\LogModel;

use zibo\orm\scaffold\view\LogDataView;

class LogController extends AbstractController {

    public $useModels = LogModel::NAME;

    public function indexAction($modelName, $id) {
        $logs = $this->models[LogModel::NAME]->getLog($modelName, $id, null, LocalizeController::getLocale());

        $view = new LogDataView($logs);
        $this->response->setView($view);
    }

}