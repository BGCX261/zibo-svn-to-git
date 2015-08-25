<?php

/**
 * @package zibo-library-filesystem
 */

namespace zibo\library\filesystem;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\Number;

/**
 * Format properties of a File object
 */
class Formatter {

    /**
     * Format a size in bytes into a more human readable byte unit
     * @param int $size size in bytes
     * @return int a size in Kb, Mb, ... depending on the size
     * @throws zibo\library\filesystem\exception\FileSystemException when the size is not a zero or a positive number
     */
    public static function formatSize($size) {
        if (!Number::isNumeric($size, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new FileSystemException('Invalid is not a valid file size');
        }

        if ($size == 0) {
            return '0 bytes';
        }

        $fileSizeUnits = array(' bytes', ' Kb', ' Mb', ' Gb', ' Tb', ' Pb', ' Eb', ' Zb', ' Yb');
        $i = floor(log($size, 1024));
        return round($size / pow(1024, $i), 2) . $fileSizeUnits[$i];
    }

}