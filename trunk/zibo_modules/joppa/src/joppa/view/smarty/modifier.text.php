<?php

use joppa\model\content\TextParser;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\html\HtmlParser;

/**
 * Parses the Joppa variables from the provided string, and makes all anchors and images absolute
 * @param string $string
 * @return string Parsed string
 */
function smarty_modifier_text($string) {
	if (!$string) {
		return '';
	}

	$request = Zibo::getInstance()->getRequest();
	$baseUrl = $request->getBaseUrl() . Request::QUERY_SEPARATOR;

	$textParser = new TextParser();
	$string = $textParser->parseText($string);

	$htmlParser = new HtmlParser($string);
	$htmlParser->makeAnchorsAbsolute($baseUrl);
	$htmlParser->makeImagesAbsolute($baseUrl);

	return $htmlParser->getHtml();
}