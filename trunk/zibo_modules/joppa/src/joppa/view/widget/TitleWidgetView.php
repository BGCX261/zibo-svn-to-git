<?php

namespace joppa\view\widget;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the title widget
 */
class TitleWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/widget/title/title';

    /**
     * Construct this view
     * @param string $title
     * @param integer $level
     * @param string $styleClass
     * @param string $styleId
     * @return null
     */
	public function __construct($title, $level = 1, $styleClass = null, $styleId = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('title', $title);
		$this->set('level', $level);
		$this->set('styleClass', $styleClass);
		$this->set('styleId', $styleId);
	}

}