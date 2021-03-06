<?php

namespace zibo\library\html\form\field;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Converter of date formats between JQuery date picker and PHP
 */
class DatepickerFormatConverter {

    /**
     * Array with php date format characters as key and datepicker format characters as value
     * @var array
     */
    private $formatCharacters = array(
        'j' => 'd',
        'd' => 'dd',
        'D' => 'D',
        'l' => 'DD',
        'n' => 'm',
        'm' => 'mm',
        'M' => 'M',
        'F' => 'MM',
        'y' => 'y',
        'Y' => 'yy',
    );

    /**
     * Convert a PHP date format to a datepicker format
     * @param string $format PHP date format
     * @return string Datepicker date format of the PHP date format
     */
    public function convertFormatFromPhp($format) {
        if (String::isEmpty($format)) {
            throw new ZiboException('Provided format is empty');
        }

        $converted = '';
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            $char = $format[$i];
            if (isset($this->formatCharacters[$char])) {
                $converted .= $this->formatCharacters[$char];
            } else {
                $converted .= $char;
            }
        }

        return $converted;
    }

}