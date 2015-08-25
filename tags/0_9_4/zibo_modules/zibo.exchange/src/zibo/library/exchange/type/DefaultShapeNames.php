<?php

namespace zibo\library\exchange\type;

/**
 * The BaseShape element identifies the set of properties to return in an item or folder response.
 */
class DefaultShapeNames {

    /**
     * Returns a set of properties that are defined as the default for the item or folder.
     * @var string
     */
    const SHAPE_DEFAULT = 'Default';

    /**
     * Returns all the properties used by the Exchange Business Logic layer to construct a folder.
     * @var string
     */
    const SHAPE_ALL_PROPERTIES = 'AllProperties';

    /**
     * Returns only the item or folder ID.
     * @var string
     */
    const SHAPE_ID_ONLY = 'IdOnly';

    /**
     * Checks if a shape is a valid shape
     * @param string $shape
     * @return boolean True when the shape is valid, false otherwise
     */
    public static function isValidShape($shape) {
        $shapes = array(
            self::SHAPE_DEFAULT,
            self::SHAPE_ALL_PROPERTIES,
            self::SHAPE_ID_ONLY
        );

        return in_array($shape, $shapes);
    }

}