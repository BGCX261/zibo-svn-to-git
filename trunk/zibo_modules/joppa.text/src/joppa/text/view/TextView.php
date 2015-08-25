<?php

namespace joppa\text\view;

use joppa\text\model\data\TextData;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the text widget
 */
class TextView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/text/text';

	/**
	 * Constructs a new text view
	 * @param joppa\text\model\TextData $text The text to display
	 * @return null
	 */
	public function __construct(TextData $text) {
		parent::__construct(self::TEMPLATE);

		$this->set('text', $text);
	}

}