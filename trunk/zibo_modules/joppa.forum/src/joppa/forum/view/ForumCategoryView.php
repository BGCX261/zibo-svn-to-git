<?php

namespace joppa\forum\view;

/**
 * View for the index of the forum frontend
 */
class ForumCategoryView extends ForumView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/category';

	/**
	 * Construct a new forum index view
	 * @param array $categories Array with forum categories and their boards
	 * @param string $boardAction URL
	 * @param string $topicAction URL
	 * @return null
	 */
	public function __construct($category, $boardAction, $topicAction) {
		parent::__construct(self::TEMPLATE);

		$this->set('category', $category);
		$this->set('boardAction', $boardAction);
		$this->set('topicAction', $topicAction);
	}

}