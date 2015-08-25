<?php

namespace joppa\content\view;

use joppa\content\model\ContentProperties;
use joppa\content\model\PaginationProperties;

use zibo\library\smarty\view\SmartyView;

/**
 * Abstract view for a content list
 */
abstract class AbstractContentOverviewView extends SmartyView implements ContentOverviewView {

    /**
     * Path to the javascript file for this view
     * @var string
     */
    const SCRIPT = 'web/scripts/joppa/widget/content.frontend.js';

    /**
     * Sets the content
     * @param integer $widgetId Id of the widget
     * @param array $result Array with Content objects
     * @param joppa\content\model\ContentProperties $contentProperties Properties for the view
     * @param joppa\content\model\PaginationProperties $paginationProperties Properties for the pagination
     * @param string $moreUrl URL
     * @return null
     */
    public function setContent($widgetId, array $result, ContentProperties $contentProperties, PaginationProperties $paginationProperties = null, $moreUrl = null) {
//        $template = $properties->getViewTemplate();
//        if ($template) {
//        	$this->template = $template;
//        }

    	$this->set('widgetId', $widgetId);
    	$this->set('title', $contentProperties->getTitle());
    	$this->set('emptyResultMessage', $contentProperties->getEmptyResultMessage());
    	$this->set('result', $result);
    	$this->set('pagination', $paginationProperties);

    	if ($moreUrl) {
    		$this->set('moreUrl', $moreUrl);
    		$this->set('moreLabel', $contentProperties->getMoreLabel());
    	} else {
    		$this->set('moreUrl', null);
    	}

    	if ($contentProperties->useAjaxForPagination()) {
            $this->addJavascript(self::SCRIPT);
            $this->addInlineJavascript('joppaContentInitializePagination(' . $widgetId . ')');
    	}
    }

}