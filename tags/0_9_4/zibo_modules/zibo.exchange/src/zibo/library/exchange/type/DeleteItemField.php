<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;

/**
 * The DeleteItemField element represents an operation to delete a given property from an item during an UpdateItem call.
 */
class DeleteItemField extends ItemChangeDescription {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'DeleteItemField';

    /**
     * Create a new DeleteItemField element
     * @param PathToUnindexedField $fieldURI URI of the field to clear
     * @return null
     */
    public function __construct(PathToUnindexedField $fieldURI) {
        parent::__construct(self::NAME, $fieldURI);
    }

}