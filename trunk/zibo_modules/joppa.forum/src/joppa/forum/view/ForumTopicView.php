<?php

namespace joppa\forum\view;

use zibo\library\smarty\view\SmartyView;

/**
 * View of a forum topic
 */
class ForumTopicView extends ForumView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/topic';

	/**
	 * Constructs a new forum topic view
	 * @param unknown_type $pages
	 * @param unknown_type $page
	 * @param unknown_type $pageUrl
	 * @param array $posts
	 * @param unknown_type $emoticonParser
	 * @param unknown_type $postAddAction
	 * @param unknown_type $postEditAction
	 * @param unknown_type $profile
	 * @param unknown_type $isModerator
	 * @param unknown_type $topicAction
	 */
	public function __construct($pages, $page, $pageUrl, array $posts, $emoticonParser = null, $postAddAction = null, $postEditAction = null, $profile = null, $isModerator = false, $topicAction = null) {
		parent::__construct(self::TEMPLATE);
		$this->set('pages', $pages);
		$this->set('page', $page);
		$this->set('pageUrl', $pageUrl);
		$this->set('emoticonParser', $emoticonParser);
		$this->set('isModerator', $isModerator);
		$this->set('posts', $posts);
		$this->set('postAddAction', $postAddAction);
		$this->set('postEditAction', $postEditAction);
		$this->set('topicAction', $topicAction);
		$this->set('profile', $profile);
		$this->set('title', null);

		$this->addJavascript('web/scripts/forum/forum.js');
		$this->addInlineJavascript("$('#forum div.post div.text').onImagesLoad({selectorCallback: forumImagesResize});");
	}

	/**
	 * Sets the title
	 * @param string $title
	 * @return null
	 */
	public function setTitle($title) {
		$this->set('title', $title);
	}

}