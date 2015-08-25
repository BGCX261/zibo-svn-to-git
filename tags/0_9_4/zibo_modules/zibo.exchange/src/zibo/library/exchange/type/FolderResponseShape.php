<?php

namespace zibo\library\exchange\type;

use \InvalidArgumentException;

/**
 * The FolderShape element identifies the folder properties to include in a GetFolder, FindFolder, or SyncFolderHierarchy  response.
 */
class FolderResponseShape {

    /**
     * Identifies the basic configuration of properties to return in a response.
     * @var string
     */
    public $BaseShape;

    /**
     * Constructs a new FolderShape element
     * @param string $baseShape Identifies the basic configuration of properties to return in a response.
     * @return null
     */
    public function __construct($baseShape) {
        if (!DefaultShapeNames::isValidShape($baseShape)) {
            throw new InvalidArgumentException('Provided base shape is invalid');
        }

        $this->BaseShape = $baseShape;
    }

}