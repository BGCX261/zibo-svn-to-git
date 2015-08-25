<?php

namespace joppa\view\backend;

use zibo\library\smarty\view\SmartyView;
use zibo\library\widget\controller\Widget;

/**
 * View for the widget in the node content view
 */
class WidgetContentView extends SmartyView {

    /**
     * Construct this view
     * @param zibo\library\widget\controller\Widget $widget
     */
	public function __construct(Widget $widget, $widgetId, $baseUrl) {
		parent::__construct('joppa/backend/widget.content');

		$this->set('baseUrl', $baseUrl);
		$this->set('widget', $widget);
		$this->set('widgetId', $widgetId);
	}

}