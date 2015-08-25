<?php

namespace zibo\queue\table;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\orm\ModelManager;

use zibo\orm\scaffold\table\ModelTable;

use zibo\queue\model\QueueModel;
use zibo\queue\table\decorator\DetailDecorator;
use zibo\queue\table\decorator\StatusDecorator;

/**
 * Table for a queue overview
 */
class QueueTable extends ModelTable {

    /**
     * Constructs a new queue table
     * @param string $formAction URL where the form of the table will point to
     * @param string $detailAction URL where the action of a queue job will point to
     * @return null
     */
    public function __construct($formAction, $detailAction, $translator) {
        $model = ModelManager::getInstance()->getModel(QueueModel::NAME);

        parent::__construct($model, $formAction);

        $this->addDecorator(new ZebraDecorator(new DetailDecorator($detailAction)));
        $this->addDecorator(new StatusDecorator($translator));
    }

}