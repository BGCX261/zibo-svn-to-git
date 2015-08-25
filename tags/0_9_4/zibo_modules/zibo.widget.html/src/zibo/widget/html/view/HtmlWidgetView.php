<?php

namespace zibo\widget\html\view;

use zibo\library\smarty\view\SmartyView;

/**
 * Frontend view for the HTML widget
 */
class HtmlWidgetView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'widget/html/html';

    /**
     * Constructs a new HTML view
     * @param string $html HTML to display
     * @return null
     */
	public function __construct($html) {
		parent::__construct(self::TEMPLATE);

		$this->set('html', $html);
	}

}