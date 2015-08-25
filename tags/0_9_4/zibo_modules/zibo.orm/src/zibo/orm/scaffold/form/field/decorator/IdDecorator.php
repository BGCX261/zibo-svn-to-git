<?php

namespace zibo\orm\scaffold\form\field\decorator;

use zibo\library\html\form\field\decorator\ValueDecorator;
use zibo\library\orm\definition\ModelTable;

/**
 * Decorator to decorate to the primary key of the provided object
 */
class IdDecorator extends ValueDecorator {

    /**
     * Constructs a new id decorator
     * @return null
     */
    public function __construct() {
        parent::__construct(ModelTable::PRIMARY_KEY);
    }

}