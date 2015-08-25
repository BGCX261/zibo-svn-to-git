<?php

use joppa\model\content\ContentFacade;

use zibo\core\Zibo;

use zibo\library\html\Anchor;

use \Exception;

/**
 * Creates a anchor to the provided data
 * @param string $data The primary key of the content or the data object pf the content
 * @param string $type The name of the content type
 * @return string Parsed string
 */
function smarty_modifier_contentUrl($data, $type = null) {
	if (!$data) {
		return '';
	}

	if (!$type) {
		return '';
	}

	$contentFacade = ContentFacade::getInstance();

	try {
	   return $contentFacade->getUrl($type, $data);
	} catch (Exception $exception) {
		Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
		return '';
	}
}