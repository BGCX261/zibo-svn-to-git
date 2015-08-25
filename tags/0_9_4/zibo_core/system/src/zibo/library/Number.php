<?php

namespace zibo\library;

use zibo\ZiboException;

/**
 * Number helper library
 */
class Number {

    /**
     * Checks if a number is a negative value
     * @param mixed $value
     * @return boolean true if the number is negative, false otherwise
     * @throws zibo\ZiboException when the provided value is not a numeric value
     */
    public static function isNegative($value) {
        if (!is_numeric($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            throw new ZiboException($value . ' is not a number');
        }

        return $value < 0;
    }

    /**
     * Checks if a numeric value is an octal value or not
     * @param mixed $value
     * @return boolean true if the provided value is an octal value, false otherwise
     */
    public static function isOctal($value) {
        if (!is_numeric($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            }
            throw new ZiboException($value . ' is not a number');
        }

        return decoct(octdec($value)) == $value;
    }

}