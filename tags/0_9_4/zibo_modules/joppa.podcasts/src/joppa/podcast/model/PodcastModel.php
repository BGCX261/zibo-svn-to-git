<?php

namespace joppa\podcast\model;

use joppa\model\SlugModel;

use zibo\library\DateTime;
use zibo\library\String;

/**
 * Model for the podcasts
 */
class PodcastModel extends SlugModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'Podcast';

	/**
	 * Path for the podcasts
	 * @var string
	 */
    const PATH_AUDIO = 'application/data/podcasts';

    /**
     * Path for the images
     * @var string
     */
    const PATH_IMAGE = 'application/web/images/podcasts';

    /**
     * Path to the default image
     * @var string
     */
    const DEFAULT_IMAGE = 'web/images/podcasts/default.png';

	/**
	 * Gets a podcast by it's slug
	 * @param string $slug Slug of the podcast
	 * @return joppa\podcast\model\data\PodcastData|null
	 */
	public function getPodcast($slug) {
		$now = DateTime::roundTimeToDay();

		$query = $this->createQuery(1);
		$query->addCondition('{slug} = %1%', $slug);
		$query->addCondition('{datePublication} < %1%', $now);

		return $query->queryFirst();
	}

	/**
	 * Gets a list of podcasts
	 * @param integer $page Page number
	 * @param integer $podcastsPerPage Number of podcasts per page
	 * @return array Array with PodcastData objects
	 */
	public function getPodcasts($page = 1, $podcastsPerPage = 10) {
		$now = DateTime::roundTimeToDay();
		$offset = ($page - 1) * $podcastsPerPage;

		$query = $this->createQuery(0);
		$query->addCondition('{datePublication} < %1%', $now);
		$query->addOrderBy('{datePublication} DESC, {id} DESC');
		$query->setLimit($podcastsPerPage, $offset);

		return $query->query();
	}

    /**
     * Counts all the podcasts
     * @return integer
     */
    public function countPodcasts() {
        $now = DateTime::roundTimeToDay();

        $query = $this->createQuery();
        $query->addCondition('{datePublication} < %1%', $now);

        return $query->count();
    }

    /**
     * Count the search results
     * @param string $query The search query
     * @param array $tokens Tokens of the search query
     * @return integer Number of podcasts matching the provided query
     */
    public function searchCountResults($query, array $tokens) {
        $query = $this->createSearchQuery($query, $tokens);
        return $query->count();
    }

    /**
     * Gets the search results
     * @param string $query The search query
     * @param array $tokens Tokens of the search query
     * @param integer $page Number of the page
     * @param integer $itemsPerPage Number of podcasts per page
     * @return array Array with PodcastData objects
     */
    public function searchGetResults($query, array $tokens, $page = null, $itemsPerPage = null) {
        $query = $this->createSearchQuery($query, $tokens);
        $query->addOrderBy('{datePublication} DESC');

        if ($page && $itemsPerPage) {
            $start = ($page - 1) * $itemsPerPage;
            $query->setLimit($itemsPerPage, $start);
        }

        return $query->query();
    }

    /**
     * Creates a search query
     * @param string $queryString The search query
     * @param array $tokens Tokens of the search query
     * @return zibo\library\orm\query\ModelQuery
     */
    private function createSearchQuery($queryString, array $tokens) {
        $time = DateTime::roundTimeToDay();

        $query = $this->createQuery(0);
        $query->addCondition('{datePublication} < %1%', $time);
        $query->addCondition('{title} LIKE %1% OR {text} LIKE %1%', '%' . $queryString . '%');

        return $query;
    }

    /**
     * Gets the string to base the slug upon
     * @param mixed $data The data object of the model
     * @return string
     */
    protected function getSlugString($data) {
    	if ($data->title) {
    		return $data->title;
    	}

    	return null;
    }

}