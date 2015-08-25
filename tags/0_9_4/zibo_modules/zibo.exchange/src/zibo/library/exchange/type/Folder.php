<?php

namespace zibo\library\exchange\type;

/**
 * The Folder element defines a folder to create, get, find, synchronize, or update.
 */
class Folder extends BaseFolder {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'Folder';

    /**
     * Constructs a new Folder element
     * @param string $displayName
     * @return null
     */
    public function __construct($displayName) {
        parent::__construct(self::NAME, $displayName);
    }

}
