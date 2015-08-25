<?php

use zibo\library\bbcode\BBCodeParser;

/**
 * Smarty modifier to parse the BBCode in a string
 * @param string $string Text to parse
 * @param zibo\library\bbcode\BBCodeParser $bbcodeParser optional, if not set a new default one will be used
 * @return string provided $string with the BBCode parsed
 */
function smarty_modifier_bbcode($string, BBCodeParser $bbcodeParser = null) {
    if (!$bbcodeParser) {
        $bbcodeParser = new BBCodeParser();
    }

    return $bbcodeParser->parse($string);
}