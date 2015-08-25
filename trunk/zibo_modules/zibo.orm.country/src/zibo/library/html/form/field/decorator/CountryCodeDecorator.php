<?php

namespace zibo\library\html\form\field\decorator;

use zibo\library\orm\model\data\CountryData;

/**
 * Country code decorator
 */
class CountryCodeDecorator implements Decorator {

    /**
     * Decorates a Country object to it's code
     * @param mixed $value Value to decorate
     * @return mixed The country code if a Country object was provided, the provided value otherwise
     */
    public function decorate($value) {
        if ($value instanceof CountryData) {
            return $value->code;
        }

        return $value;
    }

}