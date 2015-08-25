<?php

namespace joppa\content\view;

use joppa\content\model\ContentProperties;
use joppa\content\model\PaginationProperties;

use zibo\core\View;

/**
 * Interface for a content overview view
 */
interface ContentOverviewView extends View {

    /**
     * Sets the content
     * @param integer $widgetId Id of the widget
     * @param array $result Array with Content objects
     * @param joppa\content\model\ContentProperties $contentProperties Properties for the view
     * @param joppa\content\model\PaginationProperties $paginationProperties Properties for the pagination
     * @param string $moreUrl URL for the more link
     * @return null
     */
	public function setContent($widgetId, array $result, ContentProperties $contentProperties, PaginationProperties $paginationProperties = null, $moreUrl = null);

}