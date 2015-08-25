<?php

namespace zibo\library\exchange\type;

/**
 * The SavedItemFolderId element identifies the target folder for operations that update, send, and create items in a mailbox.
 */
class SavedItemFolderId extends TargetFolderId {

    /**
     * Name for the element of this type
     * @var string
     */
    const NAME = 'SavedItemFolderId';

    /**
     * Constructs a new SavedItemFolderId element
     * @param BaseFolderId $folderId
     * @return null
     */
    public function __construct(BaseFolderId $folderId) {
        parent::__construct(self::NAME, $folderId);
    }

}