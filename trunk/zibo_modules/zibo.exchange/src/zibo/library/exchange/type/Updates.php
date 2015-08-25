<?php

namespace zibo\library\exchange\type;

/**
 * The Updates element contains a set of elements that define append, set, and delete changes to item properties.
 */
class Updates extends NonEmptyArrayOfItemChangeDescriptions {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Updates';

    /**
     * Constructs a new Updates element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}