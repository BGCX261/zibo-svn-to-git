<?php

namespace joppa\view\backend;

use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\Site;

use zibo\library\widget\controller\Widget;

use zibo\core\View;

/**
 * View to show the properties of a widget
 */
class WidgetPropertiesView extends BaseView {

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm
     * @param joppa\model\Site $site
     * @param joppa\model\Node $node
     * @param zibo\library\widget\controller\Widget $widget
     * @param zibo\core\View $propertiesView
     */
	public function __construct(SiteSelectForm $siteSelectForm, Site $site, Node $node, Widget $widget, View $propertiesView = null) {
		parent::__construct($siteSelectForm, $site, $node, 'joppa/backend/widget.properties');

		$this->set('widget', $widget);

		if ($propertiesView) {
			$this->setSubview('properties', $propertiesView);
		}
	}

}