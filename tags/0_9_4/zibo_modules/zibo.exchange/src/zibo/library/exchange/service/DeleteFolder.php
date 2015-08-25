<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\FolderIds;

/**
 * The DeleteFolder operation deletes folders from a mailbox.
 */
class DeleteFolder {

    /**
     * A folder is permanently removed from the store.
     * @var string
     */
    const TYPE_HARD_DELETE = 'HardDelete';

    /**
     * A folder is moved to the dumpster if the dumpster is enabled.
     * @var string
     */
    const TYPE_SOFT_DELETE = 'SoftDelete';

    /**
     * A folder is moved to the Deleted Items folder.
     * @var string
     */
    const TYPE_MOVE_TO_DELETED_ITEMS = 'MoveToDeletedItems';

    /**
     * The FolderIds element contains an array of folder identifiers that are used to identify folders to delete.
     * @var zibo\library\exchange\FolderIds
     */
    public $FolderIds;

    /**
     * Describes how a folder is deleted.
     * @var string
     */
    public $DeleteType;

    /**
     * Constructs a new DeleteFolder element
     * @param zibo\library\exchange\type\FolderIds $folderIds Folder identifiers that are used to identify folders to delete.
     * @param string $deleteType Describes how a folder is deleted.
     * @return null
     * @throws InvalidArgumentException when the provided delete type is not valid
     */
    public function __construct(FolderIds $folderIds, $deleteType = null) {
        $this->FolderIds = $folderIds;
        $this->setDeleteType($deleteType);
    }

    /**
     * Sets how a folder is deleted
     * @param string $deleteType
     * @return null
     * @throws InvalidArgumentException when the provided delete type is not valid
     */
    private function setDeleteType($deleteType) {
        if ($deleteType === null) {
            $deleteType = self::TYPE_MOVE_TO_DELETED_ITEMS;
        } else if ($deleteType != self::TYPE_HARD_DELETE && $deleteType != self::TYPE_SOFT_DELETE && $deleteType != self::TYPE_MOVE_TO_DELETED_ITEMS) {
            throw new InvalidArgumentException('Provided delete type is not valid');
        }

        $this->DeleteType = $deleteType;
    }

}