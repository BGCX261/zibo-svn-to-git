<?php

namespace joppa\content\model;

use zibo\core\Zibo;

use zibo\library\i18n\translation\Translator;

/**
 * Factory of the content views
 */
class ContentViewFactory {

	/**
	 * Configuration key for the content overview views
	 * @var string
	 */
	const CONFIG_VIEWS_OVERVIEW = 'joppa.content.view.overview';

	/**
	 * Configuration key for the content overview views
	 * @var string
	 */
	const CONFIG_VIEWS_DETAIL = 'joppa.content.view.detail';

	/**
	 * Class name of the content overview view interface
	 * @var string
	 */
	const INTERFACE_OVERVIEW = 'joppa\\content\\view\\ContentOverviewView';

	/**
	 * Class name of the content detail view interface
	 * @var string
	 */
	const INTERFACE_DETAIL = 'joppa\\content\\view\\ContentDetailView';

	/**
	 * Instance of the factory
	 * @var ContentViewFactory
	 */
	private static $instance;

	/**
	 * Array with the predefined overview views
	 * @var array
	 */
	private $overviewViews;

	/**
	 * Array with the predefined detail views
	 * @var array
	 */
	private $detailViews;

	/**
	 * Constructs a new content view factory
	 * @return null
	 */
	private function __construct() {
		$this->readViews();
	}

	/**
	 * Gets the instance of the content view factory
	 * @return ContentViewFactory
	 */
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Gets an array of the overview views
	 * @return array Array with the internal name as key and the class name as value
	 */
	public function getOverviewViews() {
		return $this->overviewViews;
	}

	/**
	 * Gets an array of the detail views
	 * @return array Array with the internal name as key and the class name as value
	 */
	public function getDetailViews() {
		return $this->detailViews;
	}

	/**
	 * Reads the views
	 * @return null
	 */
	private function readViews() {
		$this->overviewViews = Zibo::getInstance()->getConfigValue(self::CONFIG_VIEWS_OVERVIEW);
		$this->detailViews = Zibo::getInstance()->getConfigValue(self::CONFIG_VIEWS_DETAIL);
	}

}