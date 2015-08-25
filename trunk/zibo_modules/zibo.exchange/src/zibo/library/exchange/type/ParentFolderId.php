<?php

namespace zibo\library\exchange\type;

/**
 * The ParentFolderId element identifies the folder in which a action is taken
 */
class ParentFolderId extends TargetFolderId {

    /**
     * Name for the element of this type
     * @var string
     */
    const NAME = 'ParentFolderId';

    /**
     * Constructs a new ParentFolderId element
     * @param BaseFolderId $folderId
     * @return null
     */
    public function __construct(BaseFolderId $folderId) {
        parent::__construct(self::NAME, $folderId);
    }

}