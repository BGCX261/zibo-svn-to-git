<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\ParentFolderId;
use zibo\library\exchange\type\Folders;

/**
 * The CreateFolder operation creates folders, calendar folders, contacts folders, tasks folders, and search folders.
 */
class CreateFolder {

    /**
     * Id of the parent folder
     * @param zibo\library\exchange\type\ParentFolderId
     */
    public $ParentFolderId;

    /**
     * Folders to create
     * @param zibo\library\exchange\type\Folders
     */
    public $Folders;

    /**
     * Constructs a new CreateFolder element
     * @param zibo\library\exchange\type\ParentFolderId $parentFolderId
     * @param zibo\library\exchange\type\Folders $folders
     * @return null
     */
    public function __construct(ParentFolderId $parentFolderId, Folders $folders) {
        $this->ParentFolderId = $parentFolderId;
        $this->Folders = $folders;
    }

}