<?php

namespace zibo\library\orm\model\data\format\modifier;

use zibo\library\Boolean;
use zibo\library\String;

/**
 * Modifier to get a preview of a block of HTML
 */
class PreviewDataFormatModifier implements DataFormatModifier {

    /**
     * Gets a preview of the provided HTML value
     * @param string $value HTML
     * @param array $arguments Array with arguments for the truncate function:
     *                         <ul>
     *                         <li>0 (boolean): flag to strip the HTML tags or not (true)</li>
     *                         <li>1 (integer): length to truncate (120)</li>
     *                         <li>2 (string) : etc string (...)</li>
     *                         <li>3 (boolean): flag to break words or not (false)</li>
     *                         </ul>
     * @return string
     */
    public function modifyValue($value, array $arguments) {
        $stripTags = true;
        $length = 120;
        $etc = '...';
        $breakWords = false;

        if (array_key_exists(0, $arguments)) {
            $stripTags = Boolean::getBoolean($arguments[0]);
        }

        if (array_key_exists(1, $arguments)) {
            $length = $arguments[1];
        }

        if (array_key_exists(2, $arguments)) {
            $etc = $arguments[2];
        }

        if (array_key_exists(3, $arguments)) {
            $breakWords = Boolean::getBoolean($arguments[3]);
        }

        return String::getPreviewString($value, $stripTags, $length, $etc, $breakWords);
    }

}