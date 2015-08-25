<?php

namespace joppa\forum\view\manager;

use joppa\forum\form\ForumBoardForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the manager of a forum board
 */
class BoardView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/manager/board';

	/**
	 * Constructs a new view
	 * @return null
	 */
	public function __construct(ForumBoardForm $boardForm) {
		parent::__construct(self::TEMPLATE);

		$this->set('boardForm', $boardForm);
	}

}