<?php

namespace zibo\library;

use zibo\ZiboException;

/**
 * String library
 */
class String {

    /**
     * Default character haystack for generating strings
     * @var string
     */
    const GENERATE_HAYSTACK = '123456789bcdfghjkmnpqrstvwxyz';

    /**
     * Generates a random string
     * @param integer $length Number of characters to generate
     * @param string $haystack String with the haystack to pick charaters from
     * @return string A random string
     * @throws zibo\ZiboException when an invalid length is provided
     * @throws zibo\ZiboException when an empty haystack is provided
     * @throws zibo\ZiboException when the requested length is greater then the length of the haystack
     */
    public static function generate($length = 8, $haystack = null) {
        $string = '';
        if ($haystack == null) {
            $haystack = self::GENERATE_HAYSTACK;
        }

        if (!is_numeric($length) && $length < 1) {
            throw new ZiboException('Invalid length provided: ' . $length);
        }

        if (self::isEmpty($haystack)) {
            throw new ZiboException('Empty haystack provided: ' . $haystack);
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
     * Checks whether a string is empty
     * @param mixed $value Value to check
     * @return boolean True if the provided value is a empty string
     * @throws zibo\ZiboException when the provided value is a object or an array
     */
    public static function isEmpty($value) {
        if (is_object($value)) {
            throw new ZiboException('Object \'' . get_class($value). '\' is not a string');
        }

        if (is_array($value)) {
            throw new ZiboException('Array is not a string');
        }

        if (is_numeric($value)) {
            return false;
        }

        return empty($value);
    }

    /**
     * Checks whether the provided string looks like an HTTP URL
     * @param string $string String to check
     * @return boolean True when the string starts with http:// or https://, false otherwise
     */
    public static function looksLikeUrl($string) {
        if (self::startsWith($string, 'http://') || self::startsWith($string, 'https://')) {
            return true;
        }

        return false;
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

        if (!is_numeric($length) || $length < 1) {
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
     * Gets a preview string for the provided HTML
     * @param string $html HTML to get a preview string
     * @param boolean $stripTags True to strip the tags, false to create entities of special HTML characters
     * @param integer $length Maximum length for the preview
     * @param string $etc String to truncate with if needed
     * @param boolean $breakWords Set to true to keep words as a whole
     * @return string A preview string of the provided HTML
     */
    public static function getPreviewString($html, $stripTags = true, $length = 120, $etc = '...', $breakWords = false) {
        if ($stripTags) {
            $html = strip_tags($html);
        } else {
            $html = htmlentities($html);
        }

        $html = self::truncate($html, $length, $etc, $breakWords);
        $html = nl2br($html);

        return $html;
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
        $lineNumber = 1;
        $output = '';

        $lines = explode("\n", $string);
        $lineCount = count($lines);
        $lineCountDigits = strlen($lineCount);
        foreach ($lines as $line) {
            $output .= str_pad($lineNumber , $lineCountDigits, '0', STR_PAD_LEFT) . ': ' . $line . "\n";
            $lineNumber++;
        }

        return $output;
    }

}