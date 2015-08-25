<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\FolderIds;
use zibo\library\exchange\type\FolderResponseShape;

/**
 * The GetFolder operation gets folders from the Exchange store.
 */
class GetFolder {

    /**
     * Identifies the properties to get for each folder identified in the FolderIds element.
     * @var FolderResponseShape
     */
    public $FolderShape;

    /**
     * Contains an array of folder identifiers that are used to identify folders to get from a mailbox in the Exchange store.
     * @var NonEmptyArrayOfBaseFolderIds
     */
    public $FolderIds;

    /**
     * Constructs a new GetFolder element
     * @param zibo\library\exchange\type\FolderIds $folderIds Contains an array of folder identifiers that are used to identify folders to get from a mailbox in the Exchange store.
     * @param zibo\library\exchange\type\FolderResponseShape $folderShape Identifies the properties to get for each folder identified in the FolderIds element.
     * @return null
     */
    public function __construct(FolderIds $folderIds, FolderResponseShape $folderShape = null) {
        if ($folderShape === null) {
            $folderShape = new FolderResponseShape(FolderResponseShape::BASE_DEFAULT);
        }

        $this->FolderShape = $folderShape;
        $this->FolderIds = $folderIds;
    }

}