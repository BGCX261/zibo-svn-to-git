<?php

namespace zibo\library\exchange\type;

/**
 * The FolderId element contains the identifier and change key of a folder.
 */
class FolderId extends BaseFolderId {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const TYPE = 'FolderId';

    /**
     * Constructs a new FolderId element
     * @param string $id
     * @param string $changeKey
     * @return null
     */
    public function __construct($id, $changeKey = null) {
        parent::__construct(self::TYPE, $id, $changeKey);
    }

}
