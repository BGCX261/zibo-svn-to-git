<?php

namespace joppa\forum\model\mapper;

use joppa\forum\controller\ForumWidget;
use joppa\forum\model\ForumPostModel;

use joppa\model\content\mapper\AbstractOrmContentMapper;
use joppa\model\content\Content;

/**
 * Content mapper for a forum post
 */
class ForumPostMapper extends AbstractOrmContentMapper {

	/**
	 * The base route of the forum
	 * @var string
	 */
	private $route;

	/**
	 * Number of posts per page
	 * @var integer
	 */
	private $postsPerPage;

	/**
	 * Constructs a new content mapper for a forum post
	 * @return null
	 */
	public function __construct() {
		parent::__construct(ForumPostModel::NAME);

		$nodes = $this->getNodesForWidget('joppa', 'forum');
		foreach ($nodes as $node) {
			$this->postsPerPage = $node->widgetProperties->getWidgetProperty(ForumWidget::PROPERTY_TOPICS_PER_PAGE, ForumWidget::DEFAULT_TOPICS_PER_PAGE);
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
		$post = $this->getObject($object);

		return new Content($post->subject, $this->getObjectRoute($post), $post->message, null, $post->dateAdded, $post);
	}

    /**
     * Get a data object from the model
     * @param int|object $object When an object is provided, the object will be returned. When a primary key is provided,
     * the data object will be looked up in the model
     * @return object
     * @throws zibo\ZiboException when the data object was not found in the model
     */
	protected function getObjectRoute($post) {
		if (!$this->route) {
			return null;
		}

		if (!$post->topicPostNumber) {
			$page = 1;
		} else {
			$page = floor(($post->topicPostNumber - 1) / $this->postsPerPage) + 1;
		}

		return $this->getBaseUrl() . '/' . $this->route . '/' . ForumWidget::ACTION_TOPIC . '/' . $post->topic->id . '/' . $page . '#post' . $post->id;
	}

}