<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\table\decorator\ActionDecorator;
use zibo\library\orm\model\Model;

/**
 * Action decorator for a model
 */
class ModelActionDecorator extends ActionDecorator {

    /**
     * Gets the href for the value of the cell
     * @param mixed $value The value to get the href from
     * @return string The href for the action of the model
     */
    protected function getHrefFromValue($value) {
        if ($value instanceof Model) {
            return $this->href . $value->getName();
        }

        $this->setWillDisplay(false);

        return null;
    }

}