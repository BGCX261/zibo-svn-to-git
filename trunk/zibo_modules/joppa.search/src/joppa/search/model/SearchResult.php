<?php

namespace joppa\search\model;

use zibo\ZiboException;

/**
 * Container of content results
 */
class SearchResult {

	/**
	 * Array with ContentResult objects
	 * @var array
	 */
    private $results;

    /**
     * Total number of search results
     * @var int
     */
    private $numResults;

    /**
     * Construct a new search result
     * @return null
     */
    public function __construct() {
        $this->results = array();
        $this->numResults = 0;
    }

    /**
     * Adds a content result
     * @param string $name Name of the content type
     * @param ContentResult $result Search result for the content type
     * @return null
     */
    public function addContentResult($name, ContentResult $result) {
        $this->results[$name] = $result;
        $this->numResults += $result->getTotalNumResults();
    }

    /**
     * Gets a content result
     * @param string $name Name of the content type
     * @return ContentResult
     * @throws zibo\ZiboException when there are no results for the provided content type
     */
    public function getContentResult($name) {
        if (!isset($this->results[$name])) {
            throw new ZiboException('Could not find any results for ' . $name);
        }
        return $this->results[$name];
    }

    /**
     * Gets the search results
     * @return array Array with the name of the content type as key and a ContentResult object as value
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * Gets the total number of search results
     * @return int
     */
    public function getNumResults() {
        return $this->numResults;
    }

}