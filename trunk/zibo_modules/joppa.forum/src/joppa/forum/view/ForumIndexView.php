<?php

namespace joppa\forum\view;

/**
 * View for the index of the forum frontend
 */
class ForumIndexView extends ForumView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/index';

	/**
	 * Construct a new forum index view
	 * @param array $categories Array with forum categories and their boards
	 * @param string $boardAction URL
	 * @param string $topicAction URL
	 * @return null
	 */
	public function __construct(array $categories, $categoryAction, $boardAction, $topicAction) {
		parent::__construct(self::TEMPLATE);

		$this->set('categories', $categories);
		$this->set('categoryAction', $categoryAction);
		$this->set('boardAction', $boardAction);
		$this->set('topicAction', $topicAction);
	}

}