<?php

namespace zibo\library\html\form\field\decorator;

use zibo\library\orm\model\data\CountryData;

/**
 * Country name decorator
 */
class CountryNameDecorator implements Decorator {

    /**
     * Decorates a Country object to it's name
     * @param mixed $value Value to decorate
     * @return mixed The country's name if a Country object was provided, the provided value otherwise
     */
    public function decorate($value) {
        if ($value instanceof CountryData) {
            return $value->name;
        }

        return $value;
    }

}