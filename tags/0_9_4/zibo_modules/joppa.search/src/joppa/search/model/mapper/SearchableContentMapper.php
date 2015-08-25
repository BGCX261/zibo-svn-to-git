<?php

namespace joppa\search\model\mapper;

use joppa\model\content\mapper\ContentMapper;

/**
 * Interface for a searchable content mapper
 */
interface SearchableContentMapper extends ContentMapper {

	/**
     * Get the search results
     * @param string $query the full query
     * @param string $queryTokens the full query parsed in tokens
     * @param int $page number of the result page (optional)
     * @param int $pageItems number of items per page (optional)
     * @return array Array with joppa\model\content\Content objects
	 */
	public function searchGetResults($query, array $queryTokens, $page = null, $pageItems = null);

	/**
     * Count the search results
     * @param string $query the full query
     * @param string $queryTokens the full query parsed in tokens
     * @return int number of search results
	 */
	public function searchCountResults($query, array $queryTokens);

}