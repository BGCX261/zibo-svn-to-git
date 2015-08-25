<?php

namespace joppa\content\view;

use joppa\content\model\ContentProperties;

use joppa\model\content\Content;

use zibo\library\smarty\view\SmartyView;

/**
 * Abstract view for a content detail view
 */
abstract class AbstractContentDetailView extends SmartyView implements ContentDetailView {

    /**
     * Sets the content
     * @param integer $widgetId Id of the widget
     * @param joppa\model\content\Content $content
     * @param joppa\content\model\ContentProperties $contentProperties Properties for the view
     * @return null
     */
    public function setContent($widgetId, Content $content, ContentProperties $contentProperties) {
    	$this->set('widgetId', $widgetId);
    	$this->set('content', $content);
    }

}