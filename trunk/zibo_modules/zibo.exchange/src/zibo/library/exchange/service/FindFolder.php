<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\DefaultShapeNames;
use zibo\library\exchange\type\FolderResponseShape;
use zibo\library\exchange\type\ParentFolderIds;

/**
 * The FindFolder operation uses Exchange Web Services to find subfolders of an identified folder and returns a set of properties that describe the set of subfolders.
 */
class FindFolder {

    /**
     * Instructs the FindFolder operation to search in all child folders of the identified parent folder and to return only the folder IDs for items that have not been deleted. This is called a deep traversal.
     * @var string
     */
    const TRAVERSAL_DEEP = 'Deep';

    /**
     * Instructs the FindFolder operation to search only the identified folder and to return only the folder IDs for items that have not been deleted. This is called a shallow traversal.
     * @var string
     */
    const TRAVERSAL_SHALLOW = 'Shallow';

    /**
     * Instructs the FindFolder operation to perform a shallow traversal search for deleted items.
     * @var string
     */
    const TRAVERSAL_SOFT_DELETED = 'SoftDeleted';

    /**
     * Defines how a search is performed
     * @var string
     */
    public $Traversal;

    /**
     * The FolderShape element identifies the folder properties to include in the response.
     * @var FolderResponseShape
     */
    public $FolderShape;

    /**
     * The ParentFolderIds element identifies folders to search.
     * @var ParentFolderIds
     */
    public $ParentFolderIds;

    /**
     * Constructs a new FindFolder element
     * @param zibo\library\exchange\type\ParentFolderIds $parentFolderIds Identifies the folders to search
     * @param zibo\library\exchange\type\FolderResponseShape $folderShape Identifies the folder properties to include in the response.
     * @param string $traversal Defines how a search is performed
     * @return null
     * @throws InvalidArgumentException when the traversal type is invalid
     */
    public function __construct(ParentFolderIds $parentFolderIds, FolderResponseShape $folderShape = null, $traversal = null) {
        $this->setTraversal($traversal);
        $this->setFolderShape($folderShape);
        $this->ParentFolderIds = $parentFolderIds;
    }

    /**
     * Sets the folder shape method, identifies the folder properties to include in the response
     * @param zibo\library\exchange\type\FolderResponseShape $folderShape Identifies the folder properties to include in the response.
     * @return null
     */
    public function setFolderShape(FolderResponseShape $folderShape = null) {
        if ($folderShape === null) {
            $folderShape = new FolderResponseShape(DefaultShapeNames::SHAPE_DEFAULT);
        }

        $this->FolderShape = $folderShape;
    }

    /**
     * Sets the traversal method, defines how a search is performed.
     * @param string $traversal
     * @return null
     * @throws InvalidArgumentException when the traversal type is invalid
     */
    public function setTraversal($traversal) {
        if ($traversal === null) {
            $traversal = self::TRAVERSAL_SHALLOW;
        } elseif ($traversal != self::TRAVERSAL_DEEP && $traversal != self::TRAVERSAL_SHALLOW && $traversal != self::TRAVERSAL_SOFT_DELETED) {
            throw new InvalidArgumentException('Traversal is a invalid value, try Deep, Shallow or SoftDeleted');
        }

        $this->Traversal = $traversal;
    }

}