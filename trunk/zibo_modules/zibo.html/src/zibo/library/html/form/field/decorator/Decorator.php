<?php

namespace zibo\library\html\form\field\decorator;

/**
 * Interface to decorate an AbstractArrayField
 */
interface Decorator {

    /**
     * Decorate a value for another context
     * @param mixed $value
     * @return mixed decorated value
     */
    public function decorate($value);

}