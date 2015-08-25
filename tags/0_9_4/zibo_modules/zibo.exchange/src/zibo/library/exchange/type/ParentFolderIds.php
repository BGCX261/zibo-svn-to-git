<?php

namespace zibo\library\exchange\type;

/**
 * The ParentFolderIds element identifies folders for the FindItem and FindFolder operations to search.
 */
class ParentFolderIds extends NonEmptyArrayOfBaseFolderIds {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'ParentFolderIds';

    /**
     * Constructs a new ParentFolderIds element
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

}