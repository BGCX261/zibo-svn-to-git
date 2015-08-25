<?php

namespace joppa\search\view;

use joppa\search\model\ContentResult;

use zibo\library\smarty\view\SmartyView;

/**
 * More-view of the search results widget
 */
class SearchResultMoreView extends SmartyView {

    /**
     * Construct this view
     * @param string $query the search query
     * @param joppa\search\model\ContentResult $result for the search query
     * @param string $urlPage base url for pagination
     * @param int $page the current page
     * @return null
     */
    public function __construct($contentName, $query, ContentResult $result, $urlPage, $page, $pages) {
        parent::__construct('joppa/search/result.more');

        $this->set('contentName', $contentName);
        $this->set('query', $query);
        $this->set('result', $result);

        $this->set('urlPage', $urlPage);
        $this->set('page', $page);
        $this->set('pages', $pages);
    }

}