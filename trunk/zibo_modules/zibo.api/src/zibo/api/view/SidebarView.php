<?php

namespace zibo\api\view;

use zibo\api\form\SearchForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the sidebar of the API views
 */
class SidebarView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'api/sidebar';

    /**
     * Construct the sidebar view
     * @param zibo\api\form\SearchForm $form the search form
     * @param array $namespaces array with the current namespaces
     * @param string $namespaceAction URL to the detail of a namespace
     * @param array $classes array with the current classes
     * @param string $classAction URL to the detail of a class
     * @param string $currentNamespace name of the current namespace
     * @param string $currentClass name of the current class
     * @return null
     */
    public function __construct(SearchForm $searchForm, array $namespaces, $namespaceAction, array $classes = null, $classAction = null, $currentNamespace = null, $currentClass = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('searchForm', $searchForm);

        $this->set('namespaces', $namespaces);
        $this->set('namespaceAction', $namespaceAction);
        $this->set('currentNamespace', $currentNamespace);

        $this->set('classes', $classes);
        $this->set('classAction', $classAction);
        $this->set('currentClass', $currentClass);
    }

}