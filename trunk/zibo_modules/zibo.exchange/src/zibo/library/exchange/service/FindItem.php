<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\DefaultShapeNames;
use zibo\library\exchange\type\ItemResponseShape;
use zibo\library\exchange\type\ParentFolderIds;
use zibo\library\exchange\type\Restriction;

/**
 * The FindItem element defines a request to find items in a mailbox.
 */
class FindItem {

    /**
     * Returns only the identities of items in the folder.
     * @var string
     */
    const TRAVERSAL_SHALLOW = 'Shallow';

    /**
     * Returns only the identities of items that are in a folder's dumpster.
     * @var string
     */
    const TRAVERSAL_SOFT_DELETED = 'SoftDeleted';

    /**
     * Defines how a search is performed
     * @var string
     */
    public $Traversal;

    /**
     * Identifies the item properties and content to include in the response
     * @var zibo\library\exchange\type\ItemResponseShape
     */
    public $ItemShape;

    /**
     * Defines a search for contact items based on alphabetical display names
     * @var zibo\library\exchange\type\ContactsView
     */
    public $ContactsView;

    /**
     * Defines the restriction or query that is used to filter items
     * @var zibo\library\exchange\type\Restriction
     */
    public $Restriction;

    /**
     * Identifies the folders to search
     * @var zibo\library\exchange\type\ParentFolderIds
     */
    public $ParentFolderIds;

    /**
     * Constructs a new FindFolder element
     * @param zibo\library\exchange\type\ParentFolderIds $parentFolderIds Identifies the folders to search
     * @param string $traversal Defines whether the search finds items in folders or the folders' dumpsters
     * @return null
     * @throws InvalidArgumentException when the provided traversal is not null or not a valid traversal string
     */
    public function __construct(ParentFolderIds $parentFolderIds, Restriction $restriction = null, ItemResponseShape $itemShape = null, $traversal = null) {
        $this->setTraversal($traversal);

        if ($itemShape === null) {
            $itemShape = new ItemResponseShape(DefaultShapeNames::SHAPE_DEFAULT);
        }

        $this->ItemShape = $itemShape;
        $this->Restriction = $restriction;
        $this->ParentFolderIds = $parentFolderIds;
    }

    /**
     * Sets the traversal method, defines how a search is performed.
     * @param string $traversal
     * @return null
     * @throws InvalidArgumentException when the provided traversal is not null or not a valid traversal string
     */
    public function setTraversal($traversal) {
        if ($traversal === null) {
            $traversal = self::TRAVERSAL_SHALLOW;
        } elseif ($traversal != self::TRAVERSAL_SHALLOW && $traversal != self::TRAVERSAL_SOFT_DELETED) {
            throw new InvalidArgumentException('Traversal is a invalid value, try Shallow or SoftDeleted');
        }

        $this->Traversal = $traversal;
    }

}