<?php

namespace zibo\spider\view;

use zibo\core\view\JsonView;

use zibo\library\spider\SpiderStatus;

class StatusView extends JsonView {

    public function __construct(SpiderStatus $status = null) {
        if ($status) {
            $json = array(
                'empty' => false,
                'current' => $status->getCurrent(),
                'visited' => $status->getVisited(),
                'gathered' => $status->getGathered(),
                'elapsed' => $status->getElapsedTime(),
                'finished' => $status->isFinished(),
            );
        } else {
            $json = array('empty' => true);
        }

        parent::__construct($json);
    }

}