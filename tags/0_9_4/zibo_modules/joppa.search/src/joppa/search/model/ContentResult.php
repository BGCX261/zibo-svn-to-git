<?php

namespace joppa\search\model;

/**
 * Search result for a content type
 */
class ContentResult {

	/**
	 * Array with Content objects
	 * @var array
	 */
    private $results;

    /**
     * Number of items in the results array
     * @var int
     */
    private $numResults;

    /**
     * Total number of items in the search result for this content type
     * @var int
     */
    private $totalNumResults;

    /**
     * Constructs a new search result for a content type
     * @param array $results Array with content objects
     * @param int $totalNumResults Total number of items in the search result
     * @return null
     */
    public function __construct(array $results, $totalNumResults = null) {
    	$this->setResults($results);
    	$this->setTotalNumResults($totalNumResults);
    }

    /**
     * Sets the results
     * @param array $results Array with content objects
     * @return null
     */
    private function setResults(array $results) {
    	$this->results = $results;
    	$this->numResults = count($results);
    }

    /**
     * Gets the results
     * @return array Array with content objects
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * Gets the number of results
     * @return int
     */
    public function getNumResults() {
        return $this->numResults;
    }

    /**
     * Sets the total number of items in the search result
     * @param int $totalNumResults
     * @return null
     */
    private function setTotalNumResults($totalNumResults) {
    	$this->totalNumResults = $totalNumResults;
    }

    /**
     * Gets the total number of items in the search result
     * @return int
     */
    public function getTotalNumResults() {
        if ($this->totalNumResults !== null) {
            return $this->totalNumResults;
        }

        return $this->numResults;
    }

}