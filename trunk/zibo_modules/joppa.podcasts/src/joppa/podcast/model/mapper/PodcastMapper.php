<?php

namespace joppa\podcast\model\mapper;


use joppa\model\content\mapper\AbstractOrmContentMapper;
use joppa\model\content\Content;

use joppa\podcast\model\PodcastModel;

use joppa\search\model\mapper\SearchableContentMapper;

/**
 * Content mapper for a podcast
 */
class PodcastMapper extends AbstractOrmContentMapper implements SearchableContentMapper {

	/**
	 * The URL to the node containing the podcast widget
	 * @var string
	 */
	private $url;

	/**
	 * Constructs a new content mapper for the podcast model
	 * @return null
	 */
	public function __construct() {
		parent::__construct(PodcastModel::NAME);

		$this->url = $this->getBaseUrl();

		$node = $this->getNodesForWidget('joppa', 'podcast', 1);
		if ($node) {
			$node = array_pop($node);
            $this->url .= '/' . $node->getRoute();
		}
	}

    /**
     * Get a generic content object of the data
     * @param mixed $data
     * @return joppa\model\content\Content
     */
	public function getContent($object) {
		$podcast = $this->getObject($object);

		return new Content($podcast->title, $this->url . '/' . $podcast->slug, $podcast->teaser, $podcast->image, $podcast->datePublication, $podcast);
	}

	/**
	 * Gets the search results
	 * @param string $query The search query string
	 * @param array $queryTokens Tokens of the query string
	 * @param integer $page Number of the page
	 * @param integer $itemsPerPage Number of items per page
	 * @return array Array with Content objects
	 */
    public function searchGetResults($query, array $queryTokens, $page = null, $itemsPerPage = null) {
        $podcasts = $this->model->searchGetResults($query, $queryTokens, $page, $itemsPerPage);

        $results = array();
        foreach ($podcasts as $podcast) {
        	$results[] = $this->getContent($podcast);
        }

        return $results;
    }

    /**
     * Counts the search results
     * @param string $query The search query string
     * @param array $queryTokens Tokens of the query string
     * @return integer Number of search results
     */
    public function searchCountResults($query, array $queryTokens) {
        return $this->model->searchCountResults($query, $queryTokens);
    }

}