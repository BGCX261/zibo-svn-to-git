<?php

namespace joppa\content\view;

use joppa\content\model\ContentProperties;

use joppa\model\content\Content;

use zibo\core\View;

/**
 * Interface for a content detail view
 */
interface ContentDetailView extends View {

    /**
     * Sets the content
     * @param integer $widgetId Id of the widget
     * @param joppa\model\content\Content
     * @param joppa\content\model\ContentProperties $contentProperties Properties for the view
     * @return null
     */
	public function setContent($widgetId, Content $content, ContentProperties $contentProperties);

}