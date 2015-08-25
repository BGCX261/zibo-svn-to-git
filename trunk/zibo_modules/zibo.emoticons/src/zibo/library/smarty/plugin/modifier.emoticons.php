<?php

use zibo\library\emoticons\EmoticonParser;

/**
 * Smarty modifier to parse the emoticons of a string
 * @param string $string text where you want the emoticons be replaced with their image
 * @param zibo\library\emoticons\EmoticonParser $emoticonParser optional, if not set a new default one will be used
 * @return string provided $string with the emoticons replaced by their image
 */
function smarty_modifier_emoticons($string, EmoticonParser $emoticonParser = null) {
	if (!$emoticonParser) {
		$emoticonParser = new EmoticonParser();
	}
    return $emoticonParser->parse($string);
}