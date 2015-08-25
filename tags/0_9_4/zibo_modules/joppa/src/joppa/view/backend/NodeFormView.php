<?php

namespace joppa\view\backend;

use joppa\form\backend\NodeForm;
use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\Site;

/**
 * View to edit the properties of a node
 */
class NodeFormView extends BaseView {

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm form to select the current site
     * @param joppa\form\backend\NodeForm $nodeForm form to edit the properties of the node
     * @param joppa\model\Site $site the current site
     * @param joppa\model\Node $node the current node (optional)
     * @param string $template template for the view (optional)
     * @return null
     */
	public function __construct(SiteSelectForm $siteSelectForm, NodeForm $nodeForm, Site $site = null, Node $node = null, $template = 'joppa/backend/node') {
		parent::__construct($siteSelectForm, $site, $node, $template);
		$this->set('node', $node);
		$this->set('form', $nodeForm);

		$nameField = $nodeForm->getField(NodeForm::FIELD_NAME);

		$this->addInlineJavascript('$("#' . $nameField->getId() . '").focus()');
	}

}