<?php

namespace joppa\podcast\view;

use zibo\library\smarty\view\SmartyView;

/**
 * Index view of the podcast widget
 */
class IndexView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/podcast/index';

	/**
	 * Constructs a new podcast index view
	 * @param array $podcasts Array with PodcastData objects
	 * @param integer $page Number of the current page
	 * @param integer $pages Total number of pages
	 * @param string $pageUrl URL for the pagination
	 */
	public function __construct(array $podcasts, $page = null, $pages = null, $pageUrl = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('podcasts', $podcasts);
		$this->set('pageUrl', $pageUrl);
		$this->set('page', $page);
		$this->set('pages', $pages);
	}

}
