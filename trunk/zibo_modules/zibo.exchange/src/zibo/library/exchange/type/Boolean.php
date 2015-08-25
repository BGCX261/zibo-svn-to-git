<?php

namespace zibo\library\exchange\type;

use zibo\library\Boolean as CoreBoolean;

/**
 * Boolean API for XML documents
 */
class Boolean {

    /**
     * XML attribute value for true
     * @var string
     */
    const VALUE_TRUE = 'true';

    /**
     * XML attribute value for false
     * @var unknown_type
     */
    const VALUE_FALSE = 'false';

    /**
     * Gets the XML boolean value for the provided value
     * @param mixed $value
     * @return string true or false
     */
    public static function getBoolean($value) {
        if ($value === null) {
            return null;
        }

        $value = CoreBoolean::getBoolean($value);

        return $value ? self::VALUE_TRUE : self::VALUE_FALSE;
    }

}