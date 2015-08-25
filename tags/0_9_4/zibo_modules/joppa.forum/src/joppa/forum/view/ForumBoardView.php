<?php

namespace joppa\forum\view;

/**
 * Frontend view of a forum board
 */
class ForumBoardView extends ForumView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/board';

	/**
	 * Constructs a new view for a forum board
	 * @param integer $pages
	 * @param integer $page
	 * @param string $pageAction
	 * @param array $topics
	 * @param string $topicAction
	 * @param string $topicAddAction
	 * @param string $topicStickyAction
	 * @param string $topicDeleteAction
	 */
	public function __construct($pages, $page, $pageAction, array $topics, $topicAction, $topicAddAction = null, $topicStickyAction = null, $topicDeleteAction = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('pages', $pages);
		$this->set('page', $page);
		$this->set('pageAction', $pageAction);
		$this->set('topics', $topics);
		$this->set('topicAction', $topicAction);
		$this->set('topicAddAction', $topicAddAction);
		$this->set('topicStickyAction', $topicStickyAction);
		$this->set('topicDeleteAction', $topicDeleteAction);
	}

}