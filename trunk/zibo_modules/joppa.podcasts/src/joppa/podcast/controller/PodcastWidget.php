<?php

namespace joppa\podcast\controller;

use joppa\podcast\model\data\PodcastData;
use joppa\podcast\model\PodcastModel;
use joppa\podcast\view\PodcastView;
use joppa\podcast\view\IndexView;

use joppa\controller\JoppaWidget;

use zibo\admin\view\DownloadView;
use zibo\admin\view\FileView;

use zibo\library\filesystem\File;
use zibo\library\Number;

/**
 * Widget to browse the podcasts
 */
class PodcastWidget extends JoppaWidget {

	/**
	 * Path to the icon of this widget
	 * @var unknown_type
	 */
	const ICON = 'web/images/widget/joppa/podcast.png';

	/**
	 * Translation key of the name of this widget
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.podcast.widget.name';

	/**
	 * Translation key for the podcast not found error
	 * @var string
	 */
	const TRANSLATION_ERROR_PODCAST_NOT_FOUND = 'joppa.podcast.error.podcast.not.found';

	/**
	 * Translation key for the podcast not downloadable error
	 * @var string
	 */
	const TRANSLATION_ERROR_PODCAST_NOT_DOWNLOADABLE = 'joppa.podcast.error.podcast.not.downloadable';

	/**
	 * Action to get the audio of a podcast
	 * @var string
	 */
	const ACTION_AUDIO = 'audio';

	/**
	 * Action to download the audio of a podcast
	 * @var string
	 */
	const ACTION_DOWNLOAD = 'download';

	/**
	 * Action to download the audio of a podcast
	 * @var string
	 */
	const ACTION_PAGE = 'page';

	/**
	 * Hook with the ORM module
	 * @var string
	 */
	public $useModels = PodcastModel::NAME;

	/**
	 * Constructs a new podcast widget
	 * @return null
	 */
	public function __construct() {
		parent::__construct(self::TRANSLATION_NAME, self::ICON);
	}

	/**
	 * Gets the allowed request parameters for this widget
	 * @return string
	 */
	public function getRequestParameters() {
		return '*';
	}

	/**
	 * Action to browse and view the podcasts
	 * @return null
	 */
	public function indexAction() {
		$parameters = func_get_args();

		if (!$parameters) {
			$this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_PAGE . '/1');
			return;
		}

		if ($parameters[0] == self::ACTION_PAGE) {
			$this->performIndexAction($parameters);
		} else {
			$this->performDetailAction($parameters);
		}
	}

    /**
     * Action to show the overview of the podcasts
     * @param array $parameters
     * @return null
     */
	private function performIndexAction(array $parameters) {
		try {
			$parameters = $this->parseArguments($parameters);
		} catch (ZiboException $exception) {
			$this->setError404();
			return;
		}

		if (!array_key_exists(self::ACTION_PAGE, $parameters)) {
			$this->setError404();
			return;
		}

		$podcastsPerPage = 10;

		$podcastCount = $this->models[PodcastModel::NAME]->countPodcasts();
		$pages = ceil($podcastCount / $podcastsPerPage);
		$page = $parameters[self::ACTION_PAGE];

		if (!is_numeric($page) || $page < 1 || $page > $pages) {
			$this->setError404();
			return;
		}

		$podcasts = $this->models[PodcastModel::NAME]->getPodcasts($page, $podcastsPerPage);

		$pageUrl = $this->request->getBasePath() . '/' . self::ACTION_PAGE . '/%page%';

		$view = new IndexView($podcasts, $page, $pages, $pageUrl);
		$this->response->setView($view);
	}

	/**
	 * Action to show the detail view of a podcast
	 * @param array $parameters
	 * @return null
	 */
	private function performDetailAction(array $parameters) {
		$slug = array_shift($parameters);

        $podcast = $this->models[PodcastModel::NAME]->getPodcast($slug);
        if (!$podcast || $parameters) {
            $this->setError404();
            return;
        }

        $basePath = $this->request->getBasePath() . '/';
        $audioUrl = $basePath . self::ACTION_AUDIO . '/' . $podcast->slug;
        $downloadUrl = null;

        if ($podcast->isDownloadable) {
            $downloadUrl = $basePath . self::ACTION_DOWNLOAD . '/' . $podcast->slug;
        }

        $this->addBreadcrumb($basePath . $podcast->slug, $podcast->title);
        $this->isContent(true);

        $view = new PodcastView($podcast, $audioUrl, $downloadUrl);
        $this->response->setView($view);
    }

	/**
	 * Action to get the audio file of a podcast
	 * @param string $slug Slug of the podcast
	 * @return null
	 */
	public function audioAction($slug) {
		$podcast = $this->models[PodcastModel::NAME]->getPodcast($slug);
		if (!$podcast) {
			$this->setError404();
			return;
		}

		$file = new File($podcast->audio);

		$view = new FileView($file);

		$this->response->setView($view);
	}

	/**
	 * Action to download a podcast
	 * @param string $slug Slug of the podcast
	 * @return null
	 */
	public function downloadAction($slug) {
		$podcast = $this->models[PodcastModel::NAME]->getPodcast($slug);
		if (!$podcast) {
			$this->setError404();
			return;
		}

		if (!$podcast->isDownloadable) {
			$this->addError(self::TRANSLATION_ERROR_PODCAST_NOT_DOWNLOADABLE);
			$this->setError404();
			return;
		}

		$file = new File($podcast->audio);

		$view = new DownloadView($file);

		$this->response->setView($view);
	}

}