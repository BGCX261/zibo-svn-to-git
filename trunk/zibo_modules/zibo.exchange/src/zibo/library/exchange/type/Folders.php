<?php

namespace zibo\library\exchange\type;

/**
 * The Folders element contains an array of folders that are used in folder operations.
 */
class Folders extends NonEmptyArrayOfFolders {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Folders';

    /**
     * Constructs a new Folders element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}