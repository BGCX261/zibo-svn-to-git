<?php

namespace joppa\view\backend;

use joppa\form\backend\RegionSelectForm;
use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\Site;

/**
 * View to manage the widgets of a region
 */
class NodeContentView extends BaseView {

    /**
     * Relative path to the stylesheet for this view
     * @var string
     */
    const STYLE = 'web/styles/joppa/content.css';

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm
     * @param joppa\form\backend\RegionSelectForm $regionSelectForm
     * @param joppa\model\Site $site
     * @param joppa\model\Node $node
     * @param string $region
     * @param array $availableWidgets
     * @param array $regionWidgets (optional)
     * @return null
     */
    public function __construct(SiteSelectForm $siteSelectForm, RegionSelectForm $regionSelectForm, Site $site, Node $node, $region, array $availableWidgets, array $regionWidgets = array(), $widgetDeleteMessage) {
		parent::__construct($siteSelectForm, $site, $node, 'joppa/backend/node.content');

		$this->set('page', $node);
		$this->set('form', $regionSelectForm);
		$this->set('region', $region);
		$this->set('availableWidgets', $availableWidgets);
		$this->set('regionWidgets', $regionWidgets);
		$this->set('baseUrl', $regionSelectForm->getAction() . '/');

        $this->addStyle(self::STYLE);
		$this->addInlineJavascript("joppaInitializeContent('" . $regionSelectForm->getAction() . "/', '" . $widgetDeleteMessage . "');");
	}

}