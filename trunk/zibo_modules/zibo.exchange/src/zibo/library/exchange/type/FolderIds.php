<?php

namespace zibo\library\exchange\type;

/**
 * The FolderIds element contains an array of folder identifiers that are used to identify folders to copy, move, get, delete, or monitor for event notifications.
 */
class FolderIds extends NonEmptyArrayOfBaseFolderIds {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'FolderIds';

    /**
     * Constructs a new FolderIds element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}