<?php

namespace joppa\forum\model\mapper;

use joppa\forum\controller\ForumProfileWidget;
use joppa\forum\model\ForumProfileModel;

use joppa\model\content\mapper\AbstractOrmContentMapper;
use joppa\model\content\Content;

/**
 * Content mapper for a forum profile
 */
class ForumProfileMapper extends AbstractOrmContentMapper {

	/**
	 * The base route of the forum
	 * @var string
	 */
	private $route;

	/**
	 * Constructs a new content mapper for a forum post
	 * @return null
	 */
	public function __construct() {
		parent::__construct(ForumProfileModel::NAME);

		$nodes = $this->getNodesForWidget('joppa', 'forumProfile');
		foreach ($nodes as $node) {
			$this->route = $node->getRoute();
			break;
		}
	}

    /**
     * Get a generic content object of the data
     * @param mixed $data
     * @return joppa\model\content\Content
     */
	public function getContent($object) {
		$profile = $this->getObject($object);

		$route = null;
		if ($this->route) {
            $route = $this->getBaseUrl() . '/' . $this->route . '/' . ForumProfileWidget::ACTION_DETAIL . '/' . $profile->id;
		}

		return new Content($profile->name, $route, null, null, null, $profile);
	}

}