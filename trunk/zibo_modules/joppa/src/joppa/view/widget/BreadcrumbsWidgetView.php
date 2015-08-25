<?php

namespace joppa\view\widget;

use zibo\library\html\Breadcrumbs;
use zibo\library\smarty\view\SmartyView;

/**
 * View of the breadcrumbs widget
 */
class BreadcrumbsWidgetView extends SmartyView {

    /**
     * Construct this view
     * @param zibo\library\html\Breadcrumbs $breadcrumbs
     * @return null
     */
	public function __construct(Breadcrumbs $breadcrumbs) {
		parent::__construct('joppa/widget/breadcrumbs/breadcrumbs');

		$this->set('breadcrumbs', $breadcrumbs);
	}

}