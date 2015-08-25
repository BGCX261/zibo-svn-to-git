<?php

namespace zibo\api\view;

use zibo\api\form\SearchForm;

/**
 * View for the API content of a namespace
 */
class NamespaceView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'api/namespace';

    /**
     * Construct the namespace view
     * @param zibo\api\form\SearchForm $form the search form
     * @param array $namespaces array with the current namespaces
     * @param string $namespaceAction URL to the detail of a namespace
     * @param array $classes array with the current classes
     * @param string $classAction URL to the detail of a class
     * @param string $currentNamespace name of the current namespace
     * @return null
     */
    public function __construct(SearchForm $searchForm, array $namespaces, $namespaceAction, array $classes = null, $classAction = null, $currentNamespace = null) {
        parent::__construct(self::TEMPLATE, $searchForm, $namespaces, $namespaceAction, $classes, $classAction, $currentNamespace);
    }

}