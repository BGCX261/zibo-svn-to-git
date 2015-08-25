<?php

namespace joppa\view\backend;

use joppa\form\backend\NodeVisibilityForm;
use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\Site;

/**
 * View to edit the visibility settings of a node
 */
class NodeVisibilityView extends BaseView {

    /**
     * Relative path to the stylesheet for this view
     * @var string
     */
    const STYLE = 'web/styles/joppa/visibility.css';

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm
     * @param joppa\form\backend\NodeVisibilityForm $nodeVisibilityForm
     * @param joppa\model\Site $site the current site
     * @param joppa\model\Node $node the current node (optional)
     */
    public function __construct(SiteSelectForm $siteSelectForm, NodeVisibilityForm $nodeVisibilityForm, Site $site, Node $node = null) {
		parent::__construct($siteSelectForm, $site, $node, 'joppa/backend/node.visibility');

		$this->set('node', $node);
		$this->set('form', $nodeVisibilityForm);

		$this->addStyle(self::STYLE);
	}

}