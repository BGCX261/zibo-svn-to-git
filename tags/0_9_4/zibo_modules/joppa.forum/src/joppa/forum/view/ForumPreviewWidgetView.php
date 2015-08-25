<?php

namespace joppa\forum\view;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the forum preview widget
 */
class ForumPreviewWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/preview';

	/**
	 * Constructs a new forum preview widget view
	 * @param array $posts The posts to display
	 * @param string $urlForum The URL to the index of the forum
	 */
	public function __construct(array $posts, $urlForum) {
		parent::__construct(self::TEMPLATE);

		$this->set('posts', $posts);
		$this->set('urlForum', $urlForum);
	}

}