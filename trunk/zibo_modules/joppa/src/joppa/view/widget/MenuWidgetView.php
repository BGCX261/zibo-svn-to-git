<?php

namespace joppa\view\widget;

use joppa\model\Node;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the menu widget
 */
class MenuWidgetView extends SmartyView {

    /**
     * Construct this view
     * @param array $tree Array with node instances and their children
     * @param joppa\model\Node $currentNode The current node
     * @param string $title optional title
     * @return null
     */
	public function __construct(array $tree, Node $currentNode, $title = null) {
		parent::__construct('joppa/widget/menu/menu');
		$this->set('items', $tree);
		$this->set('currentNode', $currentNode);
		$this->set('title', $title);
	}

}