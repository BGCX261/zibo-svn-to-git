<?php

namespace joppa\search\view;

use joppa\search\model\SearchResult;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the search results widget
 */
class SearchResultView extends SmartyView {

    /**
     * Construct this view
     * @param string $query the search query
     * @param joppa\search\model\SearchResult $result for the search query
     * @param string $urlMore base url for more results
     * @return null
     */
    public function __construct($query, SearchResult $result = null, $urlMore = null) {
        parent::__construct('joppa/search/result');

        $this->set('query', $query);
        $this->set('result', $result);
        $this->set('urlMore', $urlMore);
    }

}