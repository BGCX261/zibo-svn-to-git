<?php

namespace zibo\library;

use zibo\ZiboException;

/**
 * Boolean library
 */
class Boolean {

    /**
     * Array with values considered as true
     * @var array
     */
    private static $trueValues = array('true', 'yes', 'y', 'on', '1');

    /**
     * Array with values considered as false
     * @var array
     */
    private static $falseValues = array('false', 'no', 'n', 'off', '0');

    /**
     * Gets the boolean value
     *
     * Values considered as boolean:
     * <ul>
     * <li>true, false</li>
     * <li>yes, no</li>
     * <li>y, n</li>
     * <li>on, off</li>
     * <li>1, 0</li>
     * </ul>
     * @param mixed $value
     * @return boolean
     * @throws zibo\ZiboException when the provided value is not a valid boolean value
     */
    public static function getBoolean($value) {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value) && ($value == 1 || $value == 0)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $value = strtolower($value);
            if (in_array($value, self::$falseValues)) {
                return false;
            }
            if (in_array($value, self::$trueValues)) {
                return true;
            }
        } elseif (is_object($value)) {
            $value = get_class($value);
        } else {
            $value = 'Array';
        }

        throw new ZiboException($value . ' is not a boolean value');
    }

}