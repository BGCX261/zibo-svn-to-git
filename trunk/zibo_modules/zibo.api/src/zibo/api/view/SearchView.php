<?php

namespace zibo\api\view;

use zibo\api\form\SearchForm;

/**
 * View to search the api content of a namespace
 */
class SearchView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'api/search';

    /**
     * Construct the view to search through the api
     * @param zibo\api\form\SearchForm $form the search form
     * @param array $namespaces array with the current namespaces
     * @param string $namespaceAction url to the detail of a namespace
     * @param string $classAction url to the detail of a class
     * @param string $query the search query
     * @param array $result the search result
     * @return null
     */
    public function __construct(SearchForm $searchForm, array $namespaces, $namespaceAction, $classAction, $query, array $result) {
        parent::__construct(self::TEMPLATE, $searchForm, $namespaces, $namespaceAction, null, $classAction);

        $this->set('searchQuery', $query);
        $this->set('searchResult', $result);
    }

}