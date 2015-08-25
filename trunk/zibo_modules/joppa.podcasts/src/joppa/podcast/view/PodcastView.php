<?php

namespace joppa\podcast\view;

use joppa\podcast\model\data\PodcastData;

use zibo\jquery\jplayer\view\JPlayerView;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the detail of a podcast
 */
class PodcastView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/podcast/podcast';

	/**
	 * Constructs a new podcast detail view
	 * @param joppa\podcast\model\data\PodcastData $podcast The podcast to display
	 * @param string $audioUrl URL to the audio of this podcast
	 * @param string $downloadUrl URL to the download of this podcast
	 */
	public function __construct(PodcastData $podcast, $audioUrl, $downloadUrl = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('podcast', $podcast);
		$this->set('audioUrl', $audioUrl);
		$this->set('downloadUrl', $downloadUrl);

		$jplayer = new JPlayerView($audioUrl);

		$this->setSubview('player', $jplayer);
	}

}
