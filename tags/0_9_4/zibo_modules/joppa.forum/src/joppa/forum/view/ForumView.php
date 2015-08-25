<?php

namespace joppa\forum\view;

use zibo\library\smarty\view\SmartyView;

/**
 * Base view for the forum frontend
 */
class ForumView extends SmartyView {

	/**
	 * Path to the style of this view
	 * @var string
	 */
	const STYLE = 'web/styles/forum/forum.css';

	/**
	 * Construct a new forum view
	 * @param string $template Path to the template of this view
	 * @return null
	 */
	public function __construct($template) {
		parent::__construct($template);

		$this->addStyle(self::STYLE);
	}

}