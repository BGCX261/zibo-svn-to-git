<?php

namespace zibo\library;

use zibo\ZiboException;

/**
 * String helper library
 */
class String {

    /**
     * Option to check for string value
     * @var integer
     */
    const STRING = 0;

    /**
     * Option to check for non empty string values
     * @var integer
     */
    const NOT_EMPTY = 1;

    /**
     * Default character haystack for generating strings
     * @var string
     */
    const GENERATE_HAYSTACK = '123456789bcdfghjkmnpqrstvwxyz';

    /**
     * Checks whether a string is empty
     * @param mixed $value Value to check
     * @return boolean True if the provided value is a empty string
     * @throws zibo\ZiboException when the provided value is a object or an array
     */
    public static function isString($value, $options = self::STRING) {
        $isNumeric = is_numeric($value);
        if ($options & self::NOT_EMPTY && $value != '0' && empty($value)) {
            return false;
        }

        if (!$isNumeric && !is_string($value)) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the provided string starts with the provided start
     * @param string $string String to check
     * @param string $start String to check as start
     * @return boolean True when the provided string starts with the provided start
     */
    public static function startsWith($string, $start) {
        $startLength = strlen($start);
        return strncmp($string, $start, $startLength) == 0;
    }

    /**
     * Truncates the provided string
     * @param string $string String to truncate
     * @param integer $length Number of characters to keep
     * @param string $etc String to truncate with
     * @param boolean $breakWords Set to true to keep words as a whole
     * @return string Truncated string
     */
    public static function truncate($string, $length = 80, $etc = '...', $breakWords = false) {
        if (self::isEmpty($string)) {
            return '';
        }

        if (!Number::isNumeric($length, Number::NOT_NEGATIVE | Number::NOT_ZERO | Number::NOT_FLOAT)) {
            throw new ZiboException('Invalid length provided');
        }

        if (strlen($string) < $length) {
            return $string;
        }

        $length -= strlen($etc);
        if (!$breakWords) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
        }

        return substr($string, 0, $length) . $etc;
    }

    /**
     * Generates a random string
     * @param integer $length Number of characters to generate
     * @param string $haystack String with the haystack to pick characters from
     * @return string A random string
     * @throws zibo\ZiboException when an invalid length is provided
     * @throws zibo\ZiboException when an empty haystack is provided
     * @throws zibo\ZiboException when the requested length is greater then
     * the length of the haystack
     */
    public static function generate($length = 8, $haystack = null) {
        $string = '';
        if ($haystack == null) {
            $haystack = self::GENERATE_HAYSTACK;
        }

        if (!Number::isNumeric($length, Number::NOT_NEGATIVE | Number::NOT_ZERO | Number::NOT_FLOAT)) {
            throw new ZiboException('Could not generate a random string: invalid length provided');
        }

        if (!self::isString($haystack, self::NOT_EMPTY)) {
            throw new ZiboException('Could not generate a random string: empty or invalid haystack provided');
        }

        $haystackLength = strlen($haystack);
        if ($length > $haystackLength) {
            throw new ZiboException('Length cannot be greater than the length of the haystack. Length is ' . $length . ' and the length of the haystack is ' . $haystackLength);
        }

        $i = 0;
        while ($i < $length) {
            $char = substr($haystack, mt_rand(0, $haystackLength - 1), 1);
            if (!strstr($string, $char)) {
                $string .= $char;
                $i++;
            }
        }

        return $string;
    }

    /**
     * Gets a safe string for file name and URL usage
     * @param string $string String to make safe
     * @param string $replacement Replacement string for all non alpha numeric characters
     * @return string Safe string for file names and URLs
     */
    public static function safeString($string, $replacement = '_') {
        $encoding = mb_detect_encoding($string);
        if ($encoding != 'ASCII') {
            $string = iconv($encoding, 'ASCII//TRANSLIT//IGNORE', $string);
        }

        $string = preg_replace("/[\s]/", $replacement, $string);
        $string = preg_replace("/[^A-Za-z0-9._-]/", '', $string);

        return $string;
    }

    /**
     * Adds line numbers to the provided string
     * @param string $string String to add line numbers to
     * @return string String with line numbers added
     */
    public static function addLineNumbers($string) {
        $output = '';
        $lineNumber = 1;
        $lines = explode("\n", $string);
        $lineMaxDigits = strlen(count($lines));

        foreach ($lines as $line) {
            $output .= str_pad($lineNumber , $lineMaxDigits, '0', STR_PAD_LEFT) . ': ' . $line . "\n";
            $lineNumber++;
        }

        return $output;
    }

}