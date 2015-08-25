<?php

namespace joppa\search\model;

use joppa\model\content\ContentFacade;

use joppa\search\model\mapper\SearchableContentMapper;

use zibo\library\Number;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Facade to search for content types
 */
class SearchFacade {

    /**
     * Instance of the SearchFacade
     * @var SearchFacade
     */
    private static $instance;

	/**
	 * Array of the content mappers that implement the SearchableContentMapper interface
	 * @var array
	 */
	private $mappers;

    /**
     * Array for the parsed search queries
     * @var array
     */
    private $queryTokens;

    /**
     * Constructs this facade
     * @return null
     */
    private function __construct() {
        $this->loadMappers();
        $this->queryTokens = array();
    }

    /**
     * Gets the instance of the search facade
     * @return SearchFacade
     */
    public static function getInstance() {
    	if (!self::$instance) {
    		self::$instance = new self();
    	}

    	return self::$instance;
    }

	/**
	 * Gets the registered content types which implement SearchableContentMapper
	 * @return array Array with the name of the content type as value
	 */
	public function getTypes() {
		return array_keys($this->mappers);
	}

	/**
     * Performs a search in the searchable content types
     * @param string $query Query string to search with
     * @param int $numItems Number of items to return for each content type
     * @param string|array $types String or array with the content types to search for, null for all searchable content types.
     * @return SearchResult
	 */
	public function search($query, $numItems, $types = null) {
        if ($types && !is_array($types)) {
            $types = array($types);
        }

        $result = new SearchResult();
        foreach ($this->mappers as $type => $mapper) {
        	if ($types && !in_array($type, $types)) {
        		continue;
        	}

        	$typeResult = $this->searchContent($type, $query, $numItems);

        	$result->addContentResult($type, $typeResult);
        }

        return $result;
	}

	/**
     * Performs a search in a specific content type
     * @param string $type Name of the content type
     * @param string $query Query string to search with
     * @param int $numItems Number of items to return
     * @param int $page Page number of the result
     * @return ContentResult
	 */
	public function searchContent($type, $query, $numItems, $page = 1) {
		if (String::isEmpty($type)) {
			throw new ZiboException('Provided type is empty');
		}
		if (!array_key_exists($type, $this->mappers)) {
			throw new ZiboException('No searchable mapper found for ' . $type);
		}

		if (Number::isNegative($numItems)) {
			throw new ZiboException('Provided numItems cannot be negative');
		}

		if (Number::isNegative($page)) {
			throw new ZiboException('Provided page cannot be negative');
		}

		$queryTokens = $this->getQueryTokens($query);

		$results = $this->mappers[$type]->searchGetResults($query, $queryTokens, $page, $numItems);
		$numResults = $this->mappers[$type]->searchCountResults($query, $queryTokens);

		return new ContentResult($results, $numResults);
	}

    /**
     * Parses a full search query into tokens
     * @param string $query
     * @return array
     *
     * @todo create a decent tokenizer for a search query
     */
    private function getQueryTokens($query) {
        if (isset($this->queryTokens[$query])) {
            return $this->queryTokens[$query];
        }

        $tokens = explode(' ', $query);
        foreach ($tokens as $index => $token) {
            $tokens[$index] = trim($token);
        }

        $this->queryTokens[$query] = $tokens;

        return $tokens;
    }

    /**
     * Load the registered content types which implement SearchableContentMapper
     * @return null
     */
    private function loadMappers() {
        $this->mappers = array();

        $contentFacade = ContentFacade::getInstance();

        $types = $contentFacade->getTypes();
        foreach ($types as $type) {
            $mapper = $contentFacade->getMapper($type);
            if ($mapper instanceof SearchableContentMapper) {
                $this->mappers[$type] = $mapper;
            }
        }
    }

}